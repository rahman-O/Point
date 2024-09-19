import React, { useContext, useEffect, useState } from 'react';
import { useParams } from 'react-router-dom'; // Import useParams from react-router-dom
import LangContext from '@/components/langContext/LangContext.jsx';

export default function NewsDetails() {
	const [newsDetails, setNewsDetails] = useState(null);
	const { lang } = useContext(LangContext);
	const { id } = useParams(); // Get the id from the URL

	useEffect(() => {
		fetch(`/api/news/${id}`) // Dynamically use the id from useParams
			.then((response) => response.json())
			.then((data) => setNewsDetails(data));
	}, [id]); // Add id as a dependency so the effect re-runs when the id changes

	if (!newsDetails) return <div>Loading...</div>;

	return (
		<div className='w-full flex justify-center  items-center p-6'>
			<div className=' grid gap-4  w-1/2 pt-4'>
				<h1 className='sm:text-4xl text-lg md:text-xl font-bold uppercase'>
					{' '}
					{lang === 'en' ? newsDetails.title_en : newsDetails.title_ar}
				</h1>
				<img className='rounded' src={`/api/images/${newsDetails.image}`} />

				<div className=' flex justify-between  items-center'>
					<div>
						<p className='text-gray-600 text-sm mb-2'>
							{lang === 'en' ? newsDetails.event_time : newsDetails.event_time}
						</p>
						<p className='text-gray-800 leading-relaxed'>
							{lang === 'en' ? newsDetails.desc_en : newsDetails.desc_ar}
						</p>
					</div>
					<div>
						{lang === 'en' ? newsDetails.author_en : newsDetails.author_ar}
					</div>
				</div>
			</div>
		</div>
	);
}
