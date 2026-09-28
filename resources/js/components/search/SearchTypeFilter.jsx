import React from 'react';
import { SEARCH_TYPES, TYPE_STYLES, searchStrings } from './searchConfig.js';

export default function SearchTypeFilter({ lang, counts, total, value, onChange, className = '' }) {
	const t = searchStrings(lang);
	const options = [
		{ key: null, label: t.all, count: total },
		...SEARCH_TYPES.map((type) => ({ key: type, label: t.types[type], count: counts?.[type] ?? 0, icon: TYPE_STYLES[type].icon })),
	].filter((option) => option.key === null || option.key === value || option.count > 0);

	return (
		<div className={`no-scrollbar flex gap-2 overflow-x-auto ${className}`} role='group' aria-label={t.searchLabel}>
			{options.map(({ key, label, count, icon: Icon }) => {
				const selected = value === key;
				return (
					<button
						key={key ?? 'all'}
						type='button'
						aria-pressed={selected}
						onClick={() => onChange(key)}
						className={`inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap rounded-full border px-3 py-1.5 text-sm font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-point-purple/40 ${
							selected
								? 'border-point-purple bg-point-purple text-white'
								: 'border-gray-200 bg-white text-gray-700 hover:border-point-purple/40 hover:text-point-purple'
						}`}
					>
						{Icon && <Icon aria-hidden='true' className='text-[0.95em]' />}
						{label}
						{typeof count === 'number' && (
							<span className={`rounded-full px-1.5 text-xs tabular-nums ${selected ? 'bg-white/20' : 'bg-gray-100 text-gray-500'}`}>
								{count}
							</span>
						)}
					</button>
				);
			})}
		</div>
	);
}
