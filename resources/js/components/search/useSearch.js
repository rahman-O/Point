import { useCallback, useEffect, useState } from 'react';
import { MIN_QUERY_LENGTH } from './searchConfig.js';

// Plain fetch on purpose: the global axios interceptors show a full-page loader on every request.
export async function fetchSearch({ q, type, lang, page = 1, limit = 8 }, signal) {
	const params = new URLSearchParams({ q, lang, page: String(page), limit: String(limit) });
	if (type) params.set('type', type);

	const response = await fetch(`/api/search?${params}`, {
		headers: { Accept: 'application/json' },
		signal,
	});
	if (!response.ok) throw new Error(`Search failed with status ${response.status}`);

	return response.json();
}

export function useSearch(query, { type = null, lang = 'ar', limit = 8, delay = 250 } = {}) {
	const trimmed = query.trim();
	const tooShort = trimmed.length < MIN_QUERY_LENGTH;
	const [state, setState] = useState({ status: 'idle', data: null, loadingMore: false });
	const [attempt, setAttempt] = useState(0);

	useEffect(() => {
		if (tooShort) {
			setState({ status: 'idle', data: null, loadingMore: false });
			return undefined;
		}

		// Keep the previous results on screen while the next ones load, to avoid flicker.
		setState((prev) => ({ ...prev, status: 'loading' }));
		const controller = new AbortController();
		const timer = setTimeout(() => {
			fetchSearch({ q: trimmed, type, lang, limit }, controller.signal)
				.then((data) => setState({ status: 'success', data, loadingMore: false }))
				.catch((error) => {
					if (error.name !== 'AbortError') setState({ status: 'error', data: null, loadingMore: false });
				});
		}, delay);

		return () => {
			clearTimeout(timer);
			controller.abort();
		};
	}, [trimmed, tooShort, type, lang, limit, delay, attempt]);

	const loadMore = useCallback(() => {
		const current = state.data;
		if (!current?.has_more || state.loadingMore) return;

		setState((prev) => ({ ...prev, loadingMore: true }));
		fetchSearch({ q: trimmed, type, lang, limit, page: current.page + 1 })
			.then((next) =>
				setState((prev) =>
					prev.data?.query !== next.query
						? prev
						: {
								status: 'success',
								loadingMore: false,
								data: { ...next, results: [...prev.data.results, ...next.results] },
							}
				)
			)
			.catch(() => setState((prev) => ({ ...prev, loadingMore: false })));
	}, [state.data, state.loadingMore, trimmed, type, lang, limit]);

	const retry = useCallback(() => setAttempt((n) => n + 1), []);

	return { ...state, tooShort, retry, loadMore };
}

const RECENT_KEY = 'point:recent-searches';

export function useRecentSearches(max = 5) {
	const [recent, setRecent] = useState(() => {
		try {
			const stored = JSON.parse(localStorage.getItem(RECENT_KEY) ?? '[]');
			return Array.isArray(stored) ? stored.filter((q) => typeof q === 'string').slice(0, max) : [];
		} catch {
			return [];
		}
	});

	const persist = (next) => {
		try {
			localStorage.setItem(RECENT_KEY, JSON.stringify(next));
		} catch {
			// Storage can be unavailable (private mode); recent searches are a nicety.
		}
		return next;
	};

	const add = useCallback(
		(query) => {
			const q = query.trim();
			if (q.length < MIN_QUERY_LENGTH) return;
			setRecent((prev) => persist([q, ...prev.filter((x) => x.toLowerCase() !== q.toLowerCase())].slice(0, max)));
		},
		[max]
	);

	const clear = useCallback(() => setRecent(persist([])), []);

	return { recent, add, clear };
}
