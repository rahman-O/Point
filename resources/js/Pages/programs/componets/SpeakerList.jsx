import React, {useContext} from 'react';
import {Avatar} from 'flowbite-react';
import LangContext from '@/components/langContext/LangContext.jsx';
import {Link} from 'react-router-dom';

export function SpeakerList({session}) {
    const {lang} = useContext(LangContext);

    // Ensure speakers is an array before mapping over it
    const speakers = session?.speakers ?? [];
    const facilitatorId = session?.facilitator_id;

    return (
        <div
            className='w-full max-w-sm p-0 bg-transparent border-transparent md:ml-[65] align-items-center justify-center overflow-hidden'>
            <div className='text-center flex flex-col gap-8 justify-center md:justify-start'>
                {speakers.length > 0
                    ? speakers.map((speaker) => (
                        <Link to={`/speakers/${speaker.id}`} key={speaker.id}>
                            <div className='flex justify-start items-start content-start'>
                                <div className='flex-shrink-0'>
                                    <Avatar
                                        className='justify-center object-cover'
                                        size={window.innerWidth < 768 ? 'sm' : 'lg'}
                                        img={`api/images/${speaker.image}`}
                                        rounded
                                        alt={lang === 'en' ? speaker.name_en : speaker.name_ar}
                                    />
                                </div>
                                <div className='mx-3 flex flex-col items-start justify-center space-y-0.5 flex-1'>
        <span className='text-medium p-0 font-bold break-words'>
            {lang === 'en' ? speaker.name_en : speaker.name_ar}
            {speaker.id === facilitatorId && (
                <span className='mx-2 text-sm text-gray-500'>
                    {lang === 'en' ? '(Facilitator)' : '(ميسر)'}
                </span>
            )}
        </span>
                                    <span className='text-medium text-start'>
            {lang === 'en' ? speaker.job_en : speaker.job_ar}
        </span>
                                </div>
                            </div>
                        </Link>
                    ))
                    : ''}
            </div>
        </div>
    );
}
