import React, { useContext } from 'react';
import { FiSearch } from 'react-icons/fi';
import LangContext from '@/components/langContext/LangContext.jsx';
import { useSearchDialog } from './SearchProvider.jsx';
import { isMacPlatform, searchStrings } from './searchConfig.js';

export default function SearchTrigger({ variant = 'desktop' }) {
	const { lang } = useContext(LangContext);
	const { open } = useSearchDialog();
	const t = searchStrings(lang);
	const shortcut = isMacPlatform() ? '⌘K' : 'Ctrl K';

	if (variant === 'mobile') {
		return (
			<button
				type='button'
				onClick={() => open()}
				aria-label={t.searchLabel}
				aria-haspopup='dialog'
				className='flex h-10 w-10 items-center justify-center rounded-full text-gray-700 transition-colors hover:bg-point-purple/10 hover:text-point-purple focus:outline-none focus-visible:ring-2 focus-visible:ring-point-purple/40'
			>
				<FiSearch aria-hidden='true' className='text-xl' />
			</button>
		);
	}

	return (
		<button
			type='button'
			onClick={() => open()}
			aria-label={t.searchLabel}
			aria-haspopup='dialog'
			aria-keyshortcuts='Meta+K Control+K'
			title={`${t.searchLabel} (${shortcut})`}
			className='group flex h-9 items-center gap-2 rounded-full border border-gray-200 bg-gray-50 px-2.5 text-sm text-gray-500 transition-colors hover:border-point-purple/40 hover:bg-white hover:text-point-purple focus:outline-none focus-visible:ring-2 focus-visible:ring-point-purple/40 xl:w-40 xl:px-3'
		>
			<FiSearch aria-hidden='true' className='shrink-0 text-lg' />
			<span className='hidden xl:inline'>{t.searchShort}</span>
			<kbd
				className='ms-auto hidden rounded border border-gray-200 bg-white px-1.5 font-sans text-[0.7rem] font-semibold text-gray-400 xl:inline'
				dir='ltr'
			>
				{shortcut}
			</kbd>
		</button>
	);
}
