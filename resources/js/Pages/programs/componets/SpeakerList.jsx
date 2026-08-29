import React, { useContext } from 'react';
import LangContext from '@/components/langContext/LangContext.jsx';
import { Link } from 'react-router-dom';

// Neutral inline SVG avatar shown when a speaker image is missing or fails to load.
// Inlined so it never triggers an extra network request and can't itself 404.
const FALLBACK_AVATAR =
	'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="96" height="96"><rect width="96" height="96" rx="48" fill="%23cbd5e1"/><circle cx="48" cy="38" r="18" fill="%23ffffff"/><path d="M16 86c0-18 14-28 32-28s32 10 32 28z" fill="%23ffffff"/></svg>';

export function SpeakerList({ session }) {
	const { lang } = useContext(LangContext);
	const isAr = lang === 'ar';

	// Always work with an array, even if the API omits the relation.
	const speakers = session?.speakers ?? [];

	// The facilitator is one of the session's speakers, flagged with a tag rather than
	// listed separately. `facilitator_id` is a plain id on the raw model payload but a
	// nested speaker object on the API-resource payload, so accept both shapes.
	const rawFacilitatorId = session?.facilitator_id;
	const facilitatorId = Number(
		rawFacilitatorId !== null && typeof rawFacilitatorId === 'object'
			? rawFacilitatorId.id
			: rawFacilitatorId,
	);

	if (speakers.length === 0) {
		return null;
	}

	// The facilitator leads the session, so they head the list. Array.sort is stable,
	// so every other speaker keeps the order set in the admin panel.
	const orderedSpeakers = [...speakers].sort(
		(a, b) =>
			(Number(a.id) === facilitatorId ? 0 : 1) -
			(Number(b.id) === facilitatorId ? 0 : 1),
	);

	return (
		// In Arabic the document is dir="rtl", so `items-start` aligns rows to the
		// RIGHT automatically. The same class keeps English rows on the left.
		<ul className='flex w-full max-w-md flex-col gap-4 items-start'>
			{orderedSpeakers.map((speaker) => {
				const name = isAr ? speaker.name_ar : speaker.name_en;
				const job = isAr ? speaker.job_ar : speaker.job_en;
				const isFacilitator = Number(speaker.id) === facilitatorId;

				return (
					<li key={speaker.id}>
						<Link
							to={`/speakers/${speaker.id}`}
							className={`group flex items-center gap-4 rounded-xl px-12 py-2 transition-colors hover:bg-black/5 ${
								isAr ? 'text-right' : 'text-left'
							}`}
						>
							{/* Avatar: a fixed square box + object-cover keeps every portrait
							    perfectly circular and undistorted, regardless of its original
							    aspect ratio. Size scales up one step on larger screens via
							    Tailwind breakpoints (no JS window-resize hacks). */}
							<img
								src={`/api/images/${speaker.image}`}
								alt={name}
								loading='lazy'
								onError={(e) => {
									e.currentTarget.onerror = null;
									e.currentTarget.src = FALLBACK_AVATAR;
								}}
								className='h-14 w-14 flex-shrink-0 rounded-full object-cover ring-2 ring-point-teal/20 transition-transform duration-200 group-hover:scale-105 sm:h-16 sm:w-16'
							/>

							<div className='flex min-w-0 flex-col'>
								<span className='flex flex-wrap items-center gap-2 font-bold text-gray-900'>
									<span className='break-words'>{name}</span>
									{isFacilitator && (
										<span className='rounded-full bg-point-teal/10 px-2 py-0.5 text-xs font-medium text-point-teal'>
											{isAr ? 'ميسر' : 'Facilitator'}
										</span>
									)}
								</span>
								<span className='break-words text-sm text-gray-500'>{job}</span>
							</div>
						</Link>
					</li>
				);
			})}
		</ul>
	);
}
