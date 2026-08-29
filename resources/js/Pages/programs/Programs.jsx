import React, { useContext, useEffect, useState } from 'react';
import { TapsSessions } from './componets/TapsSessions';
import axios from 'axios';
import LangContext from '@/components/langContext/LangContext.jsx';
import { SpeakerList } from '@/Pages/programs/componets/SpeakerList.jsx';

export default function Programs() {
	const [program, setProgram] = useState(null);
	const { lang } = useContext(LangContext);
	const [activeTab, setActiveTab] = useState('day1');

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
	const timeSlots = [];
	const slotPositions = new Map();

	currentSessions.forEach((session) => {
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

	const titleOf = (session) =>
		lang === 'en'
			? session.title_en?.toUpperCase()
			: session.title_ar?.toUpperCase();

	const timeBadge = (slot) => (
		<span className='shrink-0 whitespace-nowrap rounded-md bg-point-purple/10 px-3 py-1 text-sm font-semibold text-point-purple md:text-base'>
			{slot.start?.slice(0, 5)} - {slot.end?.slice(0, 5)}
		</span>
	);

	const sessionsProgram = timeSlots.map((slot) => {
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
					{sessionsProgram}
				</div>
			) : (
				<p className='px-4'>
					{lang === 'en' ? 'No program selected' : ' لا توجد برامج محددة'}
				</p>
			)}
		</div>
	);
}
