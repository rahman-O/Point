import React, { useContext, useEffect, useState } from 'react';
import { TapsSessions } from './componets/TapsSessions';
import axios from 'axios';
import LangContext from '@/components/langContext/LangContext.jsx';
import { SpeakerList } from '@/Pages/programs/componets/SpeakerList.jsx';

export default function Programs() {
	const [porgrams, setProgram] = useState([]);
	const { lang } = useContext(LangContext);
	const [activeTab, setActiveTab] = useState(1);

	useEffect(() => {
		axios
			.get('/api/programs')
			.then((response) => {
				setProgram(response.data);
			})
			.catch((error) => {
				console.log(error);
			});
	}, []);

	const isValidIndex = activeTab >= 0 && porgrams.length > 0;
	const currentProgram = isValidIndex
		? porgrams.find((program) => program.id === activeTab)
		: null;

	const sessionsProgram3 = currentProgram?.sessions_program.map((session) => (
		<div key={session.id}>
			<div className='flex align-items-center py-4'>
				<span className='text-lg px-4 '>
					{session.start_time} - {session.end_time}
				</span>

				<span className=' text-lg font-bold'>
					{lang === 'en'
						? session.presentation_en.toUpperCase().slice(0, 40) + '...'
						: session.presentation_ar.toUpperCase().slice(0, 40) + '...'}
				</span>
			</div>

			<div className={`${lang === 'ar' ? 'pr-32' : 'pl-32'} py-2`}>
				<SpeakerList session={session} />
			</div>
		</div>
	));

	return (
		<div className='mb-[100]'>
			<h1 className='text-2xl text-center mt-12 uppercase'>Programs</h1>
			<TapsSessions
				programsArray={porgrams}
				activeTab={activeTab}
				setActiveTab={setActiveTab}
			/>

			{currentProgram ? (
				<div className='w-full text-center'>{sessionsProgram3}</div>
			) : (
				<p>No program selected</p>
			)}
		</div>
	);
}
