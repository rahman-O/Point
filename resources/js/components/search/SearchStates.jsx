import React from 'react';
import { Link } from 'react-router-dom';
import { FiAlertCircle, FiClock, FiSearch, FiTrendingUp, FiX } from 'react-icons/fi';
import { QUICK_LINKS, TYPE_STYLES, searchStrings } from './searchConfig.js';

export function ResultsSkeleton({ rows = 4 }) {
	return (
		<div className='flex flex-col gap-1 px-1 py-2' aria-hidden='true'>
			{Array.from({ length: rows }, (_, i) => (
				<div key={i} className='flex animate-pulse items-start gap-3 rounded-xl px-3 py-3'>
					<div className='h-12 w-12 shrink-0 rounded-xl bg-gray-200' />
					<div className='flex flex-1 flex-col gap-2 pt-1'>
						<div className='h-3.5 w-2/3 rounded bg-gray-200' />
						<div className='h-3 w-full rounded bg-gray-100' />
						<div className='h-3 w-1/3 rounded bg-gray-100' />
					</div>
				</div>
			))}
		</div>
	);
}

export function EmptyState({ lang, query, onPick }) {
	const t = searchStrings(lang);
	return (
		<div className='flex flex-col items-center px-6 py-10 text-center'>
			<span className='mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-2xl text-gray-400'>
				<FiSearch aria-hidden='true' />
			</span>
			<p className='break-all text-base font-semibold text-gray-800'>{t.noResultsTitle(query)}</p>
			<p className='mt-1 max-w-sm text-sm text-gray-500'>{t.noResultsHint}</p>
			<SuggestionChips lang={lang} onPick={onPick} className='mt-5 justify-center' />
		</div>
	);
}

export function ErrorState({ lang, onRetry }) {
	const t = searchStrings(lang);
	return (
		<div className='flex flex-col items-center px-6 py-10 text-center' role='alert'>
			<FiAlertCircle aria-hidden='true' className='mb-3 text-3xl text-point-crimson' />
			<p className='text-sm text-gray-700'>{t.error}</p>
			<button
				type='button'
				onClick={onRetry}
				className='mt-4 rounded-full bg-point-purple px-4 py-1.5 text-sm font-semibold text-white transition-opacity hover:opacity-90'
			>
				{t.retry}
			</button>
		</div>
	);
}

export function SuggestionChips({ lang, onPick, className = '' }) {
	const t = searchStrings(lang);
	return (
		<div className={`flex flex-wrap gap-2 ${className}`}>
			{t.suggestionsList.map((suggestion) => (
				<button
					key={suggestion}
					type='button'
					onClick={() => onPick(suggestion)}
					className='inline-flex items-center gap-1.5 rounded-full border border-gray-200 bg-white px-3 py-1.5 text-sm text-gray-700 transition-colors hover:border-point-purple/40 hover:text-point-purple focus:outline-none focus-visible:ring-2 focus-visible:ring-point-purple/40'
				>
					<FiTrendingUp aria-hidden='true' className='text-point-orange' />
					{suggestion}
				</button>
			))}
		</div>
	);
}

export function IdleState({ lang, recent, onPick, onClearRecent, onNavigate, showMinChars }) {
	const t = searchStrings(lang);
	return (
		<div className='flex flex-col gap-6 px-4 py-4'>
			{showMinChars && <p className='text-sm text-gray-500'>{t.minChars}</p>}

			{recent.length > 0 && (
				<section>
					<div className='mb-2 flex items-center justify-between'>
						<h3 className='text-xs font-bold uppercase tracking-wider text-gray-400'>{t.recent}</h3>
						<button type='button' onClick={onClearRecent} className='text-xs font-medium text-gray-400 hover:text-point-crimson'>
							{t.clearRecent}
						</button>
					</div>
					<ul className='flex flex-col'>
						{recent.map((q) => (
							<li key={q}>
								<button
									type='button'
									onClick={() => onPick(q)}
									className='flex w-full items-center gap-3 rounded-lg px-2 py-2 text-start text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus-visible:bg-gray-50'
								>
									<FiClock aria-hidden='true' className='shrink-0 text-gray-400' />
									<span className='truncate' dir='auto'>
										{q}
									</span>
								</button>
							</li>
						))}
					</ul>
				</section>
			)}

			<section>
				<h3 className='mb-2 text-xs font-bold uppercase tracking-wider text-gray-400'>{t.popular}</h3>
				<SuggestionChips lang={lang} onPick={onPick} />
			</section>

			<section>
				<h3 className='mb-2 text-xs font-bold uppercase tracking-wider text-gray-400'>{t.quickLinks}</h3>
				<div className='grid grid-cols-2 gap-2 sm:grid-cols-4'>
					{QUICK_LINKS.map(({ type, to }) => {
						const { icon: Icon, badge } = TYPE_STYLES[type];
						return (
							<Link
								key={type}
								to={to}
								onClick={onNavigate}
								className='flex items-center gap-2 rounded-xl border border-gray-100 px-3 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:border-point-purple/30 hover:bg-point-purple/[0.04] focus:outline-none focus-visible:ring-2 focus-visible:ring-point-purple/40'
							>
								<span className={`flex h-8 w-8 items-center justify-center rounded-lg ${badge}`}>
									<Icon aria-hidden='true' />
								</span>
								{t.types[type]}
							</Link>
						);
					})}
				</div>
			</section>
		</div>
	);
}

export function ClearButton({ lang, onClick }) {
	const t = searchStrings(lang);
	return (
		<button
			type='button'
			onClick={onClick}
			aria-label={t.clear}
			className='flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-700'
		>
			<FiX aria-hidden='true' />
		</button>
	);
}
