import React, { useContext } from 'react';
import { FiSearch, FiTrendingUp } from 'react-icons/fi';
import LangContext from '@/components/langContext/LangContext.jsx';
import { useSearchDialog } from './SearchProvider.jsx';
import { isMacPlatform, searchStrings } from './searchConfig.js';

export default function HomeSearchBar() {
	const { lang } = useContext(LangContext);
	const { open } = useSearchDialog();
	const t = searchStrings(lang);

	return (
		<section className='relative z-10 mx-auto -mt-2 mb-6 w-full max-w-3xl px-4 sm:-mt-8 md:-mt-10' aria-label={t.searchLabel}>
			<div className='rounded-2xl border border-gray-100 bg-white p-3 shadow-[0_12px_40px_-12px_rgba(124,44,139,0.28)] sm:p-4'>
				<button
					type='button'
					onClick={() => open()}
					aria-haspopup='dialog'
					className='flex w-full items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3.5 text-start transition-colors hover:border-point-purple/40 hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-point-purple/40 sm:py-4'
				>
					<FiSearch aria-hidden='true' className='shrink-0 text-xl text-point-purple' />
					<span className='min-w-0 flex-1 truncate text-sm text-gray-500 sm:text-base'>{t.placeholder}</span>
					<kbd className='hidden rounded-md border border-gray-200 bg-white px-2 py-0.5 font-sans text-xs font-semibold text-gray-400 sm:inline' dir='ltr'>
						{isMacPlatform() ? '⌘K' : 'Ctrl K'}
					</kbd>
					<span className='hidden rounded-lg bg-point-purple px-4 py-1.5 text-sm font-semibold text-white md:inline'>{t.searchLabel}</span>
				</button>

				<div className='mt-3 flex items-center gap-2 overflow-x-auto no-scrollbar'>
					<span className='shrink-0 text-xs font-semibold text-gray-400'>{t.suggestions}:</span>
					{t.suggestionsList.map((suggestion) => (
						<button
							key={suggestion}
							type='button'
							onClick={() => open({ query: suggestion })}
							className='inline-flex shrink-0 items-center gap-1 rounded-full bg-point-purple/[0.06] px-3 py-1 text-xs font-medium text-point-purple transition-colors hover:bg-point-purple/[0.12] focus:outline-none focus-visible:ring-2 focus-visible:ring-point-purple/40 sm:text-sm'
						>
							<FiTrendingUp aria-hidden='true' className='text-point-orange' />
							{suggestion}
						</button>
					))}
				</div>
			</div>
		</section>
	);
}
