import React, { useContext, useEffect, useRef, useState } from 'react';
import { createPortal } from 'react-dom';
import { Link, useNavigate } from 'react-router-dom';
import { AnimatePresence, motion } from 'framer-motion';
import {
	FiArrowDown,
	FiArrowRight,
	FiArrowUp,
	FiCornerDownLeft,
	FiSearch,
} from 'react-icons/fi';
import LangContext from '@/components/langContext/LangContext.jsx';
import { useRecentSearches, useSearch } from './useSearch.js';
import SearchResultsList, {
	orderResults,
	resultOptionId,
} from './SearchResultsList.jsx';
import SearchTypeFilter from './SearchTypeFilter.jsx';
import {
	ClearButton,
	EmptyState,
	ErrorState,
	IdleState,
	ResultsSkeleton,
} from './SearchStates.jsx';
import { searchStrings } from './searchConfig.js';

const ID = 'site-search';

function Kbd({ children }) {
	return (
		<kbd className='inline-flex min-w-[1.5rem] items-center justify-center rounded-md border border-gray-200 bg-white px-1.5 py-0.5 font-sans text-[0.7rem] font-semibold text-gray-500 shadow-[0_1px_0_rgba(0,0,0,0.06)]'>
			{children}
		</kbd>
	);
}

export default function SearchDialog({
	isOpen,
	initialQuery,
	initialType,
	onClose,
}) {
	const { lang } = useContext(LangContext);
	const t = searchStrings(lang);
	const navigate = useNavigate();
	const inputRef = useRef(null);
	const panelRef = useRef(null);
	const listRef = useRef(null);
	const [query, setQuery] = useState('');
	const [type, setType] = useState(null);
	const [activeIndex, setActiveIndex] = useState(-1);
	const { recent, add: addRecent, clear: clearRecent } = useRecentSearches();
	const search = useSearch(isOpen ? query : '', { type, lang, limit: 10 });
	const results = search.data?.results ?? [];
	const ordered = orderResults(results, !type);

	useEffect(() => {
		if (!isOpen) return undefined;

		if (initialQuery !== undefined) setQuery(initialQuery);
		setType(initialType ?? null);

		const { overflow } = document.body.style;
		document.body.style.overflow = 'hidden';

		return () => {
			document.body.style.overflow = overflow;
		};
	}, [isOpen, initialQuery, initialType]);

	useEffect(() => {
		setActiveIndex(results.length ? 0 : -1);
	}, [search.data]);

	useEffect(() => {
		if (activeIndex < 0) return;
		document
			.getElementById(resultOptionId(ID, activeIndex))
			?.scrollIntoView({ block: 'nearest' });
	}, [activeIndex]);

	const openResult = (result) => {
		addRecent(query);
		onClose();
		if (result.external) {
			window.open(result.url, '_blank', 'noopener,noreferrer');
		} else {
			navigate(result.url);
		}
	};

	const allResultsUrl = () => {
		const params = new URLSearchParams({ q: query.trim() });
		if (type) params.set('type', type);
		return `/search?${params}`;
	};

	const goToAllResults = () => {
		addRecent(query);
		onClose();
		navigate(allResultsUrl());
	};

	const trapFocus = (event) => {
		const focusable = panelRef.current?.querySelectorAll(
			'button, [href], input, [tabindex]:not([tabindex="-1"])',
		);
		if (!focusable?.length) return;
		const first = focusable[0];
		const last = focusable[focusable.length - 1];
		if (event.shiftKey && document.activeElement === first) {
			event.preventDefault();
			last.focus();
		} else if (!event.shiftKey && document.activeElement === last) {
			event.preventDefault();
			first.focus();
		}
	};

	const onKeyDown = (event) => {
		if (event.key === 'Escape') {
			event.preventDefault();
			onClose();
		} else if (event.key === 'Tab') {
			trapFocus(event);
		} else if (event.key === 'ArrowDown' && ordered.length) {
			event.preventDefault();
			setActiveIndex((i) => (i + 1) % ordered.length);
		} else if (event.key === 'ArrowUp' && ordered.length) {
			event.preventDefault();
			setActiveIndex((i) => (i <= 0 ? ordered.length - 1 : i - 1));
		} else if (event.key === 'Enter' && event.target === inputRef.current) {
			event.preventDefault();
			const active = ordered[activeIndex]?.result;
			if (active) openResult(active);
			else if (!search.tooShort) goToAllResults();
		}
	};

	const pickSuggestion = (value) => {
		setQuery(value);
		inputRef.current?.focus();
	};

	const showResults = results.length > 0;
	const isFirstLoad = search.status === 'loading' && !search.data;
	const isRefreshing = search.status === 'loading' && !!search.data;

	let body;
	if (search.tooShort) {
		body = (
			<IdleState
				lang={lang}
				recent={recent}
				onPick={pickSuggestion}
				onClearRecent={clearRecent}
				onNavigate={onClose}
				showMinChars={query.trim().length === 1}
			/>
		);
	} else if (search.status === 'error') {
		body = <ErrorState lang={lang} onRetry={search.retry} />;
	} else if (isFirstLoad) {
		body = <ResultsSkeleton />;
	} else if (!showResults && search.status === 'success') {
		body = (
			<EmptyState lang={lang} query={query.trim()} onPick={pickSuggestion} />
		);
	} else {
		body = (
			<div className='px-2 pb-2'>
				{search.data?.relaxed && (
					<p className='mx-2 mb-1 mt-2 rounded-lg bg-point-orange/10 px-3 py-2 text-xs text-[#8a4d06]'>
						{t.relaxed}
					</p>
				)}
				<SearchResultsList
					results={results}
					lang={lang}
					grouped={!type}
					activeIndex={activeIndex}
					idPrefix={ID}
					onSelect={openResult}
					onHover={setActiveIndex}
					compact
				/>
			</div>
		);
	}

	const total = search.data?.total ?? 0;

	return createPortal(
		<AnimatePresence>
			{isOpen && (
				<div
					className='fixed inset-0 z-[100] flex items-start justify-center sm:px-4 sm:pt-[8vh]'
					onKeyDown={onKeyDown}
				>
					<motion.div
						className='absolute inset-0 bg-gray-900/50 backdrop-blur-[2px]'
						initial={{ opacity: 0 }}
						animate={{ opacity: 1 }}
						exit={{ opacity: 0 }}
						transition={{ duration: 0.15 }}
						onClick={onClose}
						aria-hidden='true'
					/>
					<motion.div
						ref={panelRef}
						role='dialog'
						aria-modal='true'
						aria-labelledby={`${ID}-label`}
						dir={lang === 'ar' ? 'rtl' : 'ltr'}
						className={`relative flex h-full w-full flex-col overflow-hidden bg-white shadow-2xl sm:h-auto sm:max-h-[80vh] sm:max-w-2xl sm:rounded-2xl ${
							lang === 'ar' ? 'font-ar' : 'font-en'
						}`}
						initial={{ opacity: 0, y: -12, scale: 0.98 }}
						animate={{ opacity: 1, y: 0, scale: 1 }}
						exit={{ opacity: 0, y: -8, scale: 0.98 }}
						transition={{ duration: 0.16, ease: 'easeOut' }}
					>
						<label
							id={`${ID}-label`}
							htmlFor={`${ID}-input`}
							className='sr-only'
						>
							{t.searchLabel}
						</label>

						<div className='flex items-center gap-3 border-b border-gray-100 px-4 py-3 sm:px-5 sm:py-4'>
							<FiSearch
								aria-hidden='true'
								className={`shrink-0 text-xl ${isRefreshing ? 'animate-pulse text-point-purple' : 'text-gray-400'}`}
							/>
							<input
								ref={inputRef}
								id={`${ID}-input`}
								autoFocus
								type='search'
								dir='auto'
								role='combobox'
								aria-expanded={showResults}
								aria-controls={`${ID}-listbox`}
								aria-activedescendant={
									activeIndex >= 0 ? resultOptionId(ID, activeIndex) : undefined
								}
								aria-autocomplete='list'
								autoComplete='off'
								autoCorrect='off'
								spellCheck={false}
								enterKeyHint='search'
								maxLength={100}
								value={query}
								onChange={(e) => setQuery(e.target.value)}
								placeholder={t.placeholder}
								className='min-w-0 flex-1 border-0 bg-transparent p-0 text-base text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-0 sm:text-lg [&::-webkit-search-cancel-button]:hidden'
							/>
							{query && (
								<ClearButton lang={lang} onClick={() => pickSuggestion('')} />
							)}
							<button
								type='button'
								onClick={onClose}
								className='shrink-0 rounded-lg px-2 py-1 text-sm font-medium text-point-purple hover:bg-point-purple/5 sm:hidden'
							>
								{t.cancel}
							</button>
							<span className='hidden sm:inline-flex'>
								<Kbd>Esc</Kbd>
							</span>
						</div>

						{!search.tooShort && search.data && (
							<div className='border-b border-gray-100 px-4 py-2.5 sm:px-5'>
								<SearchTypeFilter
									lang={lang}
									counts={search.data.counts}
									total={Object.values(search.data.counts).reduce(
										(a, b) => a + b,
										0,
									)}
									value={type}
									onChange={setType}
								/>
							</div>
						)}

						<div
							ref={listRef}
							className='min-h-0 flex-1 overflow-y-auto overscroll-contain'
							aria-live='polite'
							aria-busy={search.status === 'loading'}
						>
							{body}
						</div>

						<div className='flex items-center justify-between gap-3 border-t border-gray-100 bg-gray-50/70 px-4 py-2.5 text-xs text-gray-500 sm:px-5'>
							<div className='hidden items-center gap-3 sm:flex'>
								<span className='inline-flex items-center gap-1'>
									<Kbd>
										<FiArrowUp aria-hidden='true' />
									</Kbd>
									<Kbd>
										<FiArrowDown aria-hidden='true' />
									</Kbd>
									{t.navigate}
								</span>
								<span className='inline-flex items-center gap-1'>
									<Kbd>
										<FiCornerDownLeft aria-hidden='true' />
									</Kbd>
									{t.open}
								</span>
							</div>
							{showResults && total > results.length ? (
								<Link
									to={allResultsUrl()}
									onClick={() => {
										addRecent(query);
										onClose();
									}}
									className='ms-auto inline-flex items-center gap-1 font-semibold text-point-purple hover:underline'
								>
									{t.viewAll(total)}
									<FiArrowRight aria-hidden='true' className='rtl:rotate-180' />
								</Link>
							) : (
								showResults && (
									<span className='ms-auto'>{t.resultsCount(total)}</span>
								)
							)}
						</div>
					</motion.div>
				</div>
			)}
		</AnimatePresence>,
		document.body,
	);
}
