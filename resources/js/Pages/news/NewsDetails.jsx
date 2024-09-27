import React, { useContext, useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import LangContext from '@/components/langContext/LangContext.jsx';

export default function NewsDetails() {
	const [newsDetails, setNewsDetails] = useState(null);
	const { lang } = useContext(LangContext);
	const { id } = useParams();

	useEffect(() => {
		fetch(`/api/news/${id}`)
			.then((response) => response.json())
			.then((data) => setNewsDetails(data));
	}, [id]);

	if (!newsDetails) return <div>Loading...</div>;

	return (
		<div className='w-full flex justify-center items-center p-6'>
			<div className=' grid gap-4  sm:w-1/2 pt-4 md:w-2/3 w-3/4'>
				<h1 className='sm:text-4xl text-lg md:text-xl font-bold uppercase'>
					{lang === 'en' ? newsDetails.title_en : newsDetails.title_ar}
				</h1>
				<img
					className='rounded w-full'
					src={`/api/images/${newsDetails.image}`}
				/>

				<div className=' flex justify-between  items-center'>
					<div>
						<div className='flex justify-between items-center'>
							<p className='text-gray-600 text-sm mb-2'>
								{lang === 'en'
									? newsDetails.event_time
									: newsDetails.event_time}
							</p>
							<div>
								{lang === 'en' ? newsDetails.author_en : newsDetails.author_ar}
							</div>
						</div>
						<p className='text-gray-800 leading-relaxed text-justify'>
							{lang === 'en' ? newsDetails.desc_en : newsDetails.desc_ar}
						</p>
					</div>
				</div>
			</div>
		</div>
	);
}
