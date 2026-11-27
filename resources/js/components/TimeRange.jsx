import React from 'react';

// Times come from the API as "HH:MM:SS" (24-hour). Visitors read 12-hour clocks, so the
// agenda shows "2:30 PM" / "2:30 م" instead of "14:30". Pure helpers first, so they are
// easy to reason about; the component below handles the bidi layout.

const PERIODS = {
	en: { am: 'AM', pm: 'PM' },
	ar: { am: 'ص', pm: 'م' },
};

/**
 * "14:30:00" → "2:30 PM" (en) or "2:30 م" (ar). Returns null for missing or malformed input
 * so callers can fall back gracefully instead of showing "NaN".
 */
export function formatTime12h(value, lang = 'en') {
	const match = /^(\d{1,2}):(\d{2})/.exec(value ?? '');
	if (!match) return null;

	const hours24 = Number(match[1]);
	const minutes = match[2];
	if (hours24 > 23 || Number(minutes) > 59) return null;

	const period = PERIODS[lang] ?? PERIODS.en;
	// 0 → 12 AM, 12 → 12 PM, 13 → 1 PM.
	const hours12 = hours24 % 12 === 0 ? 12 : hours24 % 12;

	return `${hours12}:${minutes} ${hours24 < 12 ? period.am : period.pm}`;
}

/**
 * Start and end of a slot, each isolated as left-to-right. Without the isolation the Arabic
 * period marker (a strong RTL letter) next to digits and a dash makes browsers reorder the
 * pieces ("م 5:00 – م 2:30"); with it each time reads correctly, and the two halves follow
 * the page direction, so Arabic readers still see the start first.
 */
export default function TimeRange({ start, end, lang = 'en', className = '' }) {
	const from = formatTime12h(start, lang);
	const to = formatTime12h(end, lang);
	if (!from && !to) return null;

	return (
		<span className={`inline-flex items-center gap-1 ${className}`}>
			{from && <span dir='ltr'>{from}</span>}
			{from && to && <span aria-hidden='true'>–</span>}
			{to && <span dir='ltr'>{to}</span>}
		</span>
	);
}
