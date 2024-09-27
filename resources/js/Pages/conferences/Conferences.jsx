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

	const stripHtmlTags = (content) => {
		const div = document.createElement('div');
		div.innerHTML = content;
		return div.textContent || div.innerText || '';
	};

	if (!conference) return <div>Loading...</div>;

	return (
		<div className='w-full grid justify-items-center items-center'>
			<div className='grid gap-4 sm:w-1/2 pt-4 md:w-2/3 w-3/4 '>
				<h1 className='sm:text-4xl text-lg md:text-xl font-bold uppercase'>
					{lang === 'en' ? conference.title_en : conference.title_ar}
				</h1>
				<img className='w-full' src={`/api/images/${conference.image}`} />
				<p className='text-justify'>
					{lang === 'en'
						? stripHtmlTags(conference.desc_en)
						: stripHtmlTags(conference.desc_ar)}
				</p>
			</div>
		</div>
	);
}
