import React, { useContext, useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import LangContext from '@/components/langContext/LangContext.jsx';

export default function SpeakersDetails() {
	const [speakersDetails, setSpeakersDetails] = useState(null);
	const { lang } = useContext(LangContext);
	const { id } = useParams();

	useEffect(() => {
		fetch(`/api/speakers/${id}`)
			.then((response) => response.json())
			.then((data) => setSpeakersDetails(data));
	}, [id]);
	const stripHtmlTags = (content) => {
		const div = document.createElement('div');
		div.innerHTML = content;
		return div.textContent || div.innerText || '';
	};

	if (!speakersDetails) return <div>Loading...</div>;

	return (
		<div className='w-full flex justify-center items-center p-6'>
			<div
				className={`flex justify-center items-start sm:w-1/2 w-3/4 md:w-3/4 ${
					lang === 'ar' ? 'flex-row' : ''
				}`}
			>
				<div style={{ width: '30%' }}>
					<img
						className=' h-[300px] object-cover'
						src={`/api/images/${speakersDetails.image}`}
						alt={name}
					/>
				</div>
				<div
					style={{ width: '70%' }}
					className={`${lang === 'ar' ? 'pr-4' : 'pl-4'}`}
				>
					<h1 className='sm:text-4xl text-lg md:text-xl font-bold uppercase'>
						{lang === 'en' ? speakersDetails.name_en : speakersDetails.name_ar}
					</h1>
					<div>
						<p className='text-gray-600 text-sm mb-2'>
							{lang === 'en' ? speakersDetails.job_en : speakersDetails.job_ar}
						</p>
						<p className='text-gray-800 leading-relaxed text-justify'>
							{lang === 'en'
								? stripHtmlTags(speakersDetails.desc_en)
								: stripHtmlTags(speakersDetails.desc_ar)}
						</p>
					</div>
				</div>
			</div>
		</div>
	);
}
