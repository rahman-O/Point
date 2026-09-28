import React from 'react';
import SearchResultItem from './SearchResultItem.jsx';
import { SEARCH_TYPES, searchStrings } from './searchConfig.js';

export const resultOptionId = (prefix, index) => `${prefix}-option-${index}`;

/**
 * When showing "All", results are grouped by type (in relevance order of each group's
 * best hit) so people can scan by category; a filtered view keeps pure relevance order.
 */
export function orderResults(results, grouped) {
	if (!grouped) return results.map((result, index) => ({ result, index }));

	const groups = new Map();
	results.forEach((result) => {
		if (!groups.has(result.type)) groups.set(result.type, []);
		groups.get(result.type).push(result);
	});

	let index = 0;
	return [...groups.values()]
		.flat()
		.map((result) => ({ result, index: index++ }));
}

export default function SearchResultsList({
	results,
	lang,
	grouped,
	activeIndex = -1,
	idPrefix,
	onSelect,
	onHover,
	compact,
}) {
	const t = searchStrings(lang);
	const ordered = orderResults(results, grouped);
	let lastType = null;

	return (
		<div
			role='listbox'
			id={`${idPrefix}-listbox`}
			aria-label={t.searchLabel}
			className='flex flex-col gap-0.5'
		>
			{ordered.map(({ result, index }) => {
				const header =
					grouped &&
					result.type !== lastType &&
					SEARCH_TYPES.includes(result.type) ? (
						<div
							role='presentation'
							className='px-3 pb-1 pt-3 text-xs font-bold uppercase tracking-wider text-gray-400 first:pt-1'
						>
							{t.types[result.type]}
						</div>
					) : null;
				lastType = result.type;

				return (
					<React.Fragment key={`${result.type}-${result.id}`}>
						{header}
						<SearchResultItem
							id={resultOptionId(idPrefix, index)}
							result={result}
							lang={lang}
							active={index === activeIndex}
							onSelect={onSelect}
							onHover={onHover ? () => onHover(index) : undefined}
							compact={compact}
						/>
					</React.Fragment>
				);
			})}
		</div>
	);
}
