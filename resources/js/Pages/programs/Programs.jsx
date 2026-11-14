import React, { useContext, useEffect, useState } from 'react';
import { useSearchParams } from 'react-router-dom';
import { TapsSessions } from './componets/TapsSessions';
import axios from 'axios';
import LangContext from '@/components/langContext/LangContext.jsx';
import { SpeakerList } from '@/Pages/programs/componets/SpeakerList.jsx';

export default function Programs() {
	const [program, setProgram] = useState(null);
	const { lang } = useContext(LangContext);
	const [activeTab, setActiveTab] = useState('day1');
	const [searchParams] = useSearchParams();
	const dayParam = searchParams.get('day');

	// Search results deep-link to a specific day, e.g. /programs?day=day2.
	useEffect(() => {
		if (/^day[1-5]$/.test(dayParam ?? '')) {
			setActiveTab(dayParam);
		}
	}, [dayParam]);

	useEffect(() => {
		axios
			.get('/api/programs/current/year')
			.then((response) => {
				setProgram(response.data);
			})
			.catch((error) => {
				console.log(error);
			});
	}, []);

	const isValidIndex = program && program[activeTab];
	const currentSessions = isValidIndex
		? program.sessions_program.filter((session) => session.day === activeTab)
		: [];

	// Sessions sharing a start and end time run in parallel and are shown together
	// under one time badge. First-seen order is kept so the agenda still follows the
	// sequence set in the admin panel.
	const groupIntoTimeSlots = (sessions) => {
		const timeSlots = [];
		const slotPositions = new Map();

		sessions.forEach((session) => {
			const key = `${session.start_time}-${session.end_time}`;

			if (!slotPositions.has(key)) {
				slotPositions.set(key, timeSlots.length);
				timeSlots.push({
					key,
					start: session.start_time,
					end: session.end_time,
					sessions: [],
				});
			}

			timeSlots[slotPositions.get(key)].sessions.push(session);
		});

		// Inside a slot, admins can number items (1 shows first); unnumbered ones (0)
		// follow in their usual order.
		timeSlots.forEach((slot) => {
			slot.sessions = slot.sessions
				.map((session, index) => ({ session, index }))
				.sort(
					(a, b) =>
						(a.session.priority || Infinity) - (b.session.priority || Infinity) ||
						a.index - b.index,
				)
				.map(({ session }) => session);
		});

		return timeSlots;
	};

	const titleOf = (session) =>
		lang === 'en'
			? session.title_en?.toUpperCase()
			: session.title_ar?.toUpperCase();

	const timeBadge = (slot) => (
		<span className='shrink-0 whitespace-nowrap rounded-md bg-point-purple/10 px-3 py-1 text-sm font-semibold text-point-purple md:text-base'>
			{slot.start?.slice(0, 5)} - {slot.end?.slice(0, 5)}
		</span>
	);

	const renderTimeSlot = (slot) => {
		if (slot.sessions.length === 1) {
			const session = slot.sessions[0];

			return (
				<div
					key={slot.key}
					className='border-b border-gray-100 py-5 last:border-b-0'
				>
					<div className='flex items-start gap-4 w-full'>
						{timeBadge(slot)}

						<h3
							className={`flex-1 min-w-0 break-words text-base font-bold leading-snug md:text-lg ${
								lang === 'ar' ? 'arabic-font text-right' : 'text-left'
							}`}
						>
							{titleOf(session)}
						</h3>
					</div>

					<div
						className={`mt-3 flex ${
							lang === 'ar'
								? 'justify-start pr-4 md:pr-32 arabic-font'
								: 'justify-start pl-4 md:pl-32'
						}`}
					>
						<SpeakerList session={session} />
					</div>
				</div>
			);
		}

		return (
			<div
				key={slot.key}
				className='border-b border-gray-100 py-5 last:border-b-0'
			>
				<div className='flex flex-wrap items-center gap-3'>
					{timeBadge(slot)}

					<span className='shrink-0 rounded-md bg-point-teal/10 px-3 py-1 text-xs font-semibold text-point-teal'>
						{lang === 'ar'
							? `جلسات متقاطعة (${slot.sessions.length})`
							: `Parallel Sessions (${slot.sessions.length})`}
					</span>
				</div>

				<div className='mt-4 grid gap-4 md:grid-cols-2'>
					{slot.sessions.map((session) => (
						<div
							key={session.id}
							className='h-full rounded-xl border border-gray-100 bg-white p-4 shadow-sm transition-shadow hover:shadow-md'
						>
							<h3
								className={`break-words text-base font-bold leading-snug md:text-lg ${
									lang === 'ar' ? 'arabic-font text-right' : 'text-left'
								}`}
							>
								{titleOf(session)}
							</h3>

							<div
								className={`mt-3 flex ${
									lang === 'ar' ? 'justify-start arabic-font' : 'justify-start'
								}`}
							>
								<SpeakerList session={session} />
							</div>
						</div>
					))}
				</div>
			</div>
		);
	};

	// One section per category, always in the order the API lists them. Items are
	// matched by `category`; a section with nothing on the selected day says so.
	const categories = program?.categories ?? [];
	const agendaSections = categories.map((category) => {
		const slots = groupIntoTimeSlots(
			currentSessions.filter((session) => session.category === category.key),
		);

		return (
			<section key={category.key} className='pt-10 first:pt-6'>
				<div className='flex items-center gap-4'>
					<h2
						className={`shrink-0 text-lg font-bold uppercase tracking-wide text-point-purple md:text-xl ${
							lang === 'ar' ? 'arabic-font' : ''
						}`}
					>
						{lang === 'ar' ? category.label_ar : category.label_en}
					</h2>
					<div className='h-px flex-1 bg-gray-200' />
				</div>

				{slots.length > 0 ? (
					slots.map(renderTimeSlot)
				) : (
					<p
						className={`mt-5 rounded-lg border border-dashed border-gray-200 bg-gray-50 px-4 py-5 text-sm text-gray-500 ${
							lang === 'ar' ? 'arabic-font' : ''
						}`}
					>
						{lang === 'ar' ? category.empty_ar : category.empty_en}
					</p>
				)}
			</section>
		);
	});

	return (
		<div className='mb-24'>
			<h1
				className={`text-2xl text-center mt-12 uppercase ${
					lang === 'ar' ? 'arabic-font' : ''
				}`}
			>
				{lang === 'en' ? 'Agenda' : 'جدول الاعمال'}
			</h1>
			<TapsSessions
				activeTab={activeTab}
				setActiveTab={setActiveTab}
				program={program}
			/>

			{program ? (
				<div
					className={`w-full px-4 md:px-24 text-center ${lang === 'ar' ? ' arabic-font' : ''}`}
				>
					{agendaSections}
				</div>
			) : (
				<p className='px-4'>
					{lang === 'en' ? 'No program selected' : ' لا توجد برامج محددة'}
				</p>
			)}
		</div>
	);
}
