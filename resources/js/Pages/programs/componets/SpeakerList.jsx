import React, { useContext } from 'react';
import { Avatar } from 'flowbite-react';
import LangContext from '@/components/langContext/LangContext.jsx';

export function SpeakerList({ session }) {
	const { lang } = useContext(LangContext);

	// Ensure speakers is an array before mapping over it
	const speakers = session?.speakers ?? [];

	return (
		<div className='w-full max-w-sm p-0 bg-transparent border-transparent md:ml-[65] align-items-center justify-center'>
			<div className='text-center flex flex-col gap-8 justify-center md:justify-start'>
				{speakers.length > 0 ? (
					speakers.map((speaker) => (
						<div key={speaker.id} className='flex mx-3 mb-3 gap-4'>
							<Avatar
								className='justify-center'
								size='lg'
								img={`api/images/${speaker.image}`}
								rounded
								alt={lang === 'en' ? speaker.name_en : speaker.name_ar}
							/>
							<div className='ml-3 grid justify-items-start items-center gap-0'>
								<span className='text-medium p-0 font-bold'>
									{lang === 'en' ? speaker.name_en : speaker.name_ar}
								</span>
								<span className='text-medium -mt-6'>
									{lang === 'en' ? speaker.job_en : speaker.job_ar}
								</span>
							</div>
						</div>
					))
				) : (
					<p>No speakers available for this session.</p>
				)}
			</div>
		</div>
	);
}
