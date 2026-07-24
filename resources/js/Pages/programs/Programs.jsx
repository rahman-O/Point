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

	const sessionsProgram = currentSessions.map((session) => (
		<div
			key={session.id}
			className='border-b border-gray-100 py-5 last:border-b-0'
		>
			<div className='flex items-start gap-4 w-full'>
				<span className='shrink-0 whitespace-nowrap rounded-md bg-point-purple/10 px-3 py-1 text-sm font-semibold text-point-purple md:text-base'>
					{session.start_time.slice(0, 5)} - {session.end_time.slice(0, 5)}
				</span>

				<h3
					className={`flex-1 min-w-0 break-words text-base font-bold leading-snug md:text-lg ${
						lang === 'ar' ? 'arabic-font text-right' : 'text-left'
					}`}
				>
					{lang === 'en'
						? session.title_en?.toUpperCase()
						: session.title_ar?.toUpperCase()}
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
	));

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
