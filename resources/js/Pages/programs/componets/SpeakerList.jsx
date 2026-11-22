import React, { useContext } from 'react';
import LangContext from '@/components/langContext/LangContext.jsx';
import { Link } from 'react-router-dom';
import { FiMic, FiEdit3, FiAward, FiStar, FiBookOpen, FiVolume2 } from 'react-icons/fi';

// Neutral inline SVG avatar shown when a speaker image is missing or fails to load.
// Inlined so it never triggers an extra network request and can't itself 404.
const FALLBACK_AVATAR =
	'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="96" height="96"><rect width="96" height="96" rx="48" fill="%23cbd5e1"/><circle cx="48" cy="38" r="18" fill="%23ffffff"/><path d="M16 86c0-18 14-28 32-28s32 10 32 28z" fill="%23ffffff"/></svg>';

// Optional roles a session's speakers can hold, in the order they head the list. Each has
// its own colour so they are told apart at a glance. Full class names are spelled out so
// Tailwind keeps them in the build.
const ROLES = [
	{
		key: 'chair_id',
		en: 'Session chair',
		ar: 'إدارة الجلسة',
		Icon: FiStar,
		ring: 'ring-[3px] ring-point-rose ring-offset-2',
		badge: 'bg-point-rose text-white',
	},
	{
		// The only role several speakers can share: the API sends a list under this key.
		key: 'mcs',
		en: 'Master of ceremonies',
		ar: 'العريف',
		Icon: FiVolume2,
		ring: 'ring-[3px] ring-point-emerald ring-offset-2',
		badge: 'bg-point-emerald text-white',
	},
	{
		key: 'facilitator_id',
		en: 'Facilitator',
		ar: 'ميسر الجلسة',
		Icon: FiMic,
		ring: 'ring-[3px] ring-point-purple ring-offset-2',
		badge: 'bg-point-purple text-white',
	},
	{
		key: 'trainer_id',
		en: 'Trainer',
		ar: 'مدرب',
		Icon: FiAward,
		ring: 'ring-[3px] ring-point-orange ring-offset-2',
		badge: 'bg-point-orange text-white',
	},
	{
		key: 'lecturer_id',
		en: 'Lecturer',
		ar: 'محاضر',
		Icon: FiBookOpen,
		ring: 'ring-[3px] ring-point-indigo ring-offset-2',
		badge: 'bg-point-indigo text-white',
	},
	{
		key: 'rapporteur_id',
		en: 'Rapporteur',
		ar: 'مقرر الجلسة',
		Icon: FiEdit3,
		ring: 'ring-[3px] ring-point-teal ring-offset-2',
		badge: 'bg-point-teal text-white',
	},
];

// Role ids are plain ids on the raw model payload but nested speaker objects on the
// API-resource payload, so accept both shapes.
const idOf = (value) =>
	Number(value !== null && typeof value === 'object' ? value.id : value);

// Whether a speaker holds a role; a role's value is a single id/speaker or, for roles
// several speakers can share, a list of them.
const holdsRole = (session, role, speakerId) => {
	const value = session?.[role.key];
	const holders = Array.isArray(value) ? value : [value];
	return holders.some((holder) => idOf(holder) === Number(speakerId));
};

// One row of the list: avatar + name (+ role badges) + subtitle. Speakers link to their
// profile page; custom participants have no page, so they render as plain rows.
function PersonRow({ to, image, name, subtitle, roles, isAr }) {
	const primaryRole = roles[0];
	const rowClass = `group flex items-center gap-4 rounded-xl px-12 py-2 ${
		isAr ? 'text-right' : 'text-left'
	}`;

	const content = (
		<>
			{/* Avatar: a fixed square box + object-cover keeps every portrait perfectly
			    circular and undistorted, regardless of its original aspect ratio. */}
			<img
				src={`/api/images/${image}`}
				alt={name}
				loading='lazy'
				onError={(e) => {
					e.currentTarget.onerror = null;
					e.currentTarget.src = FALLBACK_AVATAR;
				}}
				className={`h-14 w-14 flex-shrink-0 rounded-full object-cover object-top transition-transform duration-200 group-hover:scale-105 sm:h-16 sm:w-16 ${
					primaryRole ? primaryRole.ring : 'ring-2 ring-point-teal/20'
				}`}
			/>

			<div className='flex min-w-0 flex-col'>
				<span className='flex flex-wrap items-center gap-2 font-bold text-gray-900'>
					<span className='break-words'>{name}</span>
					{roles.map((role) => (
						<span
							key={role.key}
							className={`inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold ${role.badge}`}
						>
							<role.Icon aria-hidden='true' />
							{isAr ? role.ar : role.en}
						</span>
					))}
				</span>
				{subtitle && <span className='break-words text-sm text-gray-500'>{subtitle}</span>}
			</div>
		</>
	);

	return to ? (
		<Link to={to} className={`${rowClass} transition-colors hover:bg-black/5`}>
			{content}
		</Link>
	) : (
		<div className={rowClass}>{content}</div>
	);
}

export function SpeakerList({ session }) {
	const { lang } = useContext(LangContext);
	const isAr = lang === 'ar';

	// Admins list either speakers (with profiles and roles) or custom participants
	// (name, photo, optional description; no profile page) under an item.
	const participants = Array.isArray(session?.participants) ? session.participants : [];
	// Always work with an array, even if the API omits the relation.
	const speakers = session?.speakers ?? [];

	if (participants.length > 0) {
		return (
			<ul className='flex w-full max-w-md flex-col gap-4 items-start'>
				{participants.map((person, index) => (
					<li key={index}>
						<PersonRow
							image={person.image}
							name={isAr ? person.name_ar || person.name_en : person.name_en || person.name_ar}
							subtitle={isAr ? person.desc_ar || person.desc_en : person.desc_en || person.desc_ar}
							roles={[]}
							isAr={isAr}
						/>
					</li>
				))}
			</ul>
		);
	}

	if (speakers.length === 0) {
		return null;
	}

	// Which roles each speaker holds (a speaker may hold more than one).
	const rolesOf = (speaker) =>
		ROLES.filter((role) => holdsRole(session, role, speaker.id));

	// Role holders head the list in ROLES order (chair, MC, facilitator, trainer, lecturer, rapporteur).
	// Array.sort is stable, so every other speaker keeps the order set in the admin panel.
	const rank = (speaker) => {
		const index = ROLES.findIndex((role) => holdsRole(session, role, speaker.id));
		return index === -1 ? ROLES.length : index;
	};
	const orderedSpeakers = [...speakers].sort((a, b) => rank(a) - rank(b));

	return (
		// In Arabic the document is dir="rtl", so `items-start` aligns rows to the
		// RIGHT automatically. The same class keeps English rows on the left.
		<ul className='flex w-full max-w-md flex-col gap-4 items-start'>
			{orderedSpeakers.map((speaker) => (
				<li key={speaker.id}>
					<PersonRow
						to={`/speakers/${speaker.id}`}
						image={speaker.image}
						name={isAr ? speaker.name_ar : speaker.name_en}
						subtitle={isAr ? speaker.job_ar : speaker.job_en}
						roles={rolesOf(speaker)}
						isAr={isAr}
					/>
				</li>
			))}
		</ul>
	);
}
