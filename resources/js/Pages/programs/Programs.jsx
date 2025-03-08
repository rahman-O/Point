import React, {useContext, useEffect, useState} from 'react';
import {TapsSessions} from './componets/TapsSessions';
import axios from 'axios';
import LangContext from '@/components/langContext/LangContext.jsx';
import {SpeakerList} from '@/Pages/programs/componets/SpeakerList.jsx';

export default function Programs() {
    const [program, setProgram] = useState(null);
    const {lang} = useContext(LangContext);
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
        <div key={session.id}>
            <div className='flex align-items-center py-4'>
				<span className='text-lg px-4'>
					{session.start_time} - {session.end_time}
				</span>

                <span
                    className={`text-lg font-bold text-start w-1/2 ${
                        lang === 'ar' ? 'arabic-font' : ''
                    }`}
                >
					{lang === 'en'
                        ? session.title_en?.toUpperCase()
                        : session.title_ar?.toUpperCase()}
				</span>
            </div>

            <div className={`${lang === 'ar' ? 'pr-32 arabic-font' : 'pl-32'}`}>
                <SpeakerList session={session}/>
            </div>
        </div>
    ));

    return (
        <div className='mb-[100]'>
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
                    className={`w-full mx-24 text-center ${lang === 'ar' ? 'arabic-font' : ''}`}
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
