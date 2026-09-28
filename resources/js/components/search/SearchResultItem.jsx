import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { FiArrowUpRight, FiClock, FiPlay } from 'react-icons/fi';
import Highlight from './Highlight.jsx';
import { TYPE_STYLES, formatDate, searchStrings } from './searchConfig.js';

function Thumbnail({ result }) {
	const [failed, setFailed] = useState(false);
	const { icon: Icon, badge } = TYPE_STYLES[result.type] ?? TYPE_STYLES.news;

	if (!result.image || failed) {
		return (
			<span className={`flex h-12 w-12 shrink-0 items-center justify-center rounded-xl ${badge}`} aria-hidden='true'>
				<Icon className='text-xl' />
			</span>
		);
	}

	if (result.type === 'stream') {
		return (
			<span className='relative h-12 w-20 shrink-0 overflow-hidden rounded-lg bg-gray-100' aria-hidden='true'>
				<img src={result.image} alt='' loading='lazy' onError={() => setFailed(true)} className='h-full w-full object-cover' />
				<span className='absolute inset-0 flex items-center justify-center bg-black/25'>
					<FiPlay className='text-base text-white drop-shadow' />
				</span>
			</span>
		);
	}

	return (
		<img
			src={result.image}
			alt=''
			aria-hidden='true'
			loading='lazy'
			onError={() => setFailed(true)}
			className={`h-12 shrink-0 bg-gray-100 object-cover ${result.type === 'speaker' ? 'w-12 rounded-full' : 'w-16 rounded-lg'}`}
		/>
	);
}

function Meta({ result, lang }) {
	const t = searchStrings(lang);
	const parts = [];

	if (result.type === 'session' && result.extra?.day) parts.push(t.day(result.extra.day));
	if (result.type === 'session' && result.extra?.start) {
		parts.push(
			<span key='time' className='inline-flex items-center gap-1' dir='ltr'>
				<FiClock className='text-[0.8em]' />
				{result.extra.start}
				{result.extra.end ? ` – ${result.extra.end}` : ''}
			</span>
		);
	}
	if (result.date) parts.push(formatDate(result.date, lang));
	else if (result.year) parts.push(result.year);

	return (
		<span className='flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-gray-500'>
			<span className={`rounded-full px-2 py-0.5 font-semibold ${TYPE_STYLES[result.type]?.badge ?? ''}`}>
				{t.typeSingular[result.type] ?? result.type}
			</span>
			{parts.map((part, i) => (
				<React.Fragment key={i}>
					<span aria-hidden='true' className='text-gray-300'>
						•
					</span>
					{part}
				</React.Fragment>
			))}
			{result.external && (
				<span className='inline-flex items-center gap-0.5 text-gray-400'>
					<FiArrowUpRight aria-hidden='true' />
					<span className='sr-only sm:not-sr-only'>{t.opensInNewTab}</span>
				</span>
			)}
		</span>
	);
}

export default function SearchResultItem({ result, lang, active = false, id, onSelect, onHover, compact = false }) {
	const content = (
		<>
			<Thumbnail result={result} />
			<span className='flex min-w-0 flex-1 flex-col gap-1'>
				<span dir='auto' className='line-clamp-2 break-words text-[0.95rem] font-semibold leading-snug text-gray-900'>
					<Highlight segments={result.title} />
				</span>
				{result.alt_title && (
					<span dir='auto' className='line-clamp-1 text-sm text-gray-600'>
						<Highlight segments={result.alt_title} />
					</span>
				)}
				{result.subtitle && (
					<span dir='auto' className='line-clamp-1 text-sm text-gray-600'>
						<Highlight segments={result.subtitle} />
					</span>
				)}
				{result.snippet?.length > 0 && (
					<span dir='auto' className={`${compact ? 'line-clamp-1' : 'line-clamp-2'} text-sm leading-relaxed text-gray-500`}>
						<Highlight segments={result.snippet} />
					</span>
				)}
				<Meta result={result} lang={lang} />
			</span>
		</>
	);

	const className = `group flex w-full items-start gap-3 rounded-xl px-3 py-3 text-start outline-none transition-colors ${
		active ? 'bg-point-purple/[0.07] ring-1 ring-point-purple/20' : 'hover:bg-gray-50 focus-visible:bg-gray-50'
	}`;
	const common = {
		id,
		role: 'option',
		'aria-selected': active,
		className,
		onClick: () => onSelect?.(result),
		onMouseMove: onHover,
	};

	return result.external ? (
		<a {...common} href={result.url} target='_blank' rel='noopener noreferrer'>
			{content}
		</a>
	) : (
		<Link {...common} to={result.url}>
			{content}
		</Link>
	);
}
