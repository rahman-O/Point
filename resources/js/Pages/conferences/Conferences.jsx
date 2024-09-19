import React, { useContext, useEffect, useState } from 'react';
import LangContext from '@/components/langContext/LangContext.jsx';

export default function Conferences() {
	const [conference, setConference] = useState(null);
	const { lang, toggleLang } = useContext(LangContext);
	useEffect(() => {
		fetch('/api/conferences/last')
			.then((response) => response.json())
			.then((data) => setConference(data));
	}, []);

	if (!conference) return <div>Loading...</div>;

	return (
		<div className='w-full grid justify-items-center items-center'>
			<div className=' grid gap-4 w-1/2 pt-4'>
				<h1 className='sm:text-4xl text-lg md:text-xl font-bold uppercase'>
					{lang === 'en' ? conference.title_en : conference.title_ar}
				</h1>
				<img className='' src={`/api/images/${conference.image}`} />
				<p className='text-justify'>
					{lang === 'en' ? conference.desc_en : conference.desc_ar}
				</p>
			</div>
		</div>
	);
}
