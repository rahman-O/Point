import React, { useContext, useEffect, useRef, useState } from 'react';
import { useSearchParams } from 'react-router-dom';
import { FiSearch } from 'react-icons/fi';
import LangContext from '@/components/langContext/LangContext.jsx';
import { useRecentSearches, useSearch } from '@/components/search/useSearch.js';
import SearchResultsList from '@/components/search/SearchResultsList.jsx';
import SearchTypeFilter from '@/components/search/SearchTypeFilter.jsx';
import {
	ClearButton,
	EmptyState,
	ErrorState,
	IdleState,
	ResultsSkeleton,
} from '@/components/search/SearchStates.jsx';
import {
	SEARCH_TYPES,
	searchStrings,
} from '@/components/search/searchConfig.js';

export default function SearchPage() {
	const { lang } = useContext(LangContext);
	const t = searchStrings(lang);
	const [params, setParams] = useSearchParams();
	const urlQuery = params.get('q') ?? '';
	const urlType = SEARCH_TYPES.includes(params.get('type'))
		? params.get('type')
		: null;
	const [query, setQuery] = useState(urlQuery);
	const inputRef = useRef(null);
	const { recent, add: addRecent, clear: clearRecent } = useRecentSearches();
	const search = useSearch(query, {
		type: urlType,
		lang,
		limit: 20,
		delay: 300,
	});
	const results = search.data?.results ?? [];

	// Follow back/forward navigation between searches.
	useEffect(() => setQuery(urlQuery), [urlQuery]);

	// Mirror the typed query into the URL without adding a history entry per keystroke.
	useEffect(() => {
		const timer = setTimeout(() => {
			const trimmed = query.trim();
			if (trimmed === urlQuery.trim()) return;
			const next = new URLSearchParams(params);
			trimmed ? next.set('q', trimmed) : next.delete('q');
			setParams(next, { replace: true });
			if (trimmed) addRecent(trimmed);
		}, 600);
		return () => clearTimeout(timer);
	}, [query]);

	useEffect(() => {
		const trimmed = query.trim();
		document.title = trimmed
			? `${t.resultsFor(trimmed)} | Point Iraq`
			: `${t.pageTitle} | Point Iraq`;
	}, [query, lang]);

	const setType = (type) => {
		const next = new URLSearchParams(params);
		type ? next.set('type', type) : next.delete('type');
		setParams(next, { replace: true });
	};

	const pick = (value) => {
		setQuery(value);
		inputRef.current?.focus();
	};

	const counts = search.data?.counts;
	const total = counts ? Object.values(counts).reduce((a, b) => a + b, 0) : 0;

	let body;
	if (search.tooShort) {
		body = (
			<IdleState
				lang={lang}
				recent={recent}
				onPick={pick}
				onClearRecent={clearRecent}
				showMinChars={query.trim().length === 1}
			/>
		);
	} else if (search.status === 'error') {
		body = <ErrorState lang={lang} onRetry={search.retry} />;
	} else if (search.status === 'loading' && !search.data) {
		body = <ResultsSkeleton rows={6} />;
	} else if (search.status === 'success' && results.length === 0) {
		body = <EmptyState lang={lang} query={query.trim()} onPick={pick} />;
	} else {
		body = (
			<>
				<p className='mb-2 px-3 text-sm text-gray-500' aria-live='polite'>
					{t.resultsCount(search.data?.total ?? 0)}
				</p>
				{search.data?.relaxed && (
					<p className='mx-3 mb-2 rounded-lg bg-point-orange/10 px-3 py-2 text-sm text-[#8a4d06]'>
						{t.relaxed}
					</p>
				)}
				<SearchResultsList
					results={results}
					lang={lang}
					grouped={false}
					idPrefix='search-page'
					onSelect={() => addRecent(query)}
				/>
				{search.data?.has_more && (
					<div className='mt-6 flex justify-center'>
						<button
							type='button'
							onClick={search.loadMore}
							disabled={search.loadingMore}
							className='rounded-full border border-point-purple px-6 py-2 text-sm font-semibold text-point-purple transition-colors hover:bg-point-purple hover:text-white disabled:opacity-60'
						>
							{search.loadingMore ? t.searching : t.loadMore}
						</button>
					</div>
				)}
			</>
		);
	}

	return (
		<div
			className={`mx-auto w-full max-w-3xl px-4 pb-8 pt-8 sm:pt-12 ${lang === 'ar' ? 'font-ar' : 'font-en'}`}
		>
			<h1 className='text-2xl font-bold text-gray-900 sm:text-3xl'>
				{t.pageTitle}
			</h1>
			<p className='mt-1 text-sm text-gray-500 sm:text-base'>{t.pageIntro}</p>

			<form
				role='search'
				onSubmit={(e) => {
					e.preventDefault();
					addRecent(query);
				}}
				className='sticky top-2 z-20 mt-6'
			>
				<label htmlFor='search-page-input' className='sr-only'>
					{t.searchLabel}
				</label>
				<div className='flex items-center gap-3 rounded-2xl border border-gray-200 bg-white px-4 py-3 shadow-sm transition-shadow focus-within:border-point-purple/50 focus-within:shadow-[0_8px_30px_-12px_rgba(124,44,139,0.35)]'>
					<FiSearch
						aria-hidden='true'
						className={`shrink-0 text-xl ${search.status === 'loading' ? 'animate-pulse text-point-purple' : 'text-gray-400'}`}
					/>
					<input
						ref={inputRef}
						id='search-page-input'
						type='search'
						dir='auto'
						autoFocus
						autoComplete='off'
						spellCheck={false}
						enterKeyHint='search'
						maxLength={100}
						value={query}
						onChange={(e) => setQuery(e.target.value)}
						placeholder={t.placeholder}
						className='min-w-0 flex-1 border-0 bg-transparent p-0 text-base text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-0 sm:text-lg [&::-webkit-search-cancel-button]:hidden'
					/>
					{query && <ClearButton lang={lang} onClick={() => pick('')} />}
				</div>
			</form>

			{!search.tooShort && counts && (
				<SearchTypeFilter
					lang={lang}
					counts={counts}
					total={total}
					value={urlType}
					onChange={setType}
					className='mt-4'
				/>
			)}

			<div className='mt-4'>{body}</div>
		</div>
	);
}
