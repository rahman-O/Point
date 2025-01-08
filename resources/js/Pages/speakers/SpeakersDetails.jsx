import React, { useContext, useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import LangContext from '@/components/langContext/LangContext.jsx';
import { AiOutlineGlobal } from 'react-icons/ai';
import { CgWorkAlt } from 'react-icons/cg';
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
		<div className='w-full flex justify-center items-center p-4 sm:p-6'>
			<div
				className={`flex flex-col md:flex-row gap-4 w-full sm:w-4/5 lg:w-3/5 ${
					lang === 'ar' ? 'flex-row-reverse' : ''
				}`}
			>
				{/* Image Section */}
				<div className='w-full md:w-1/3 flex justify-center items-center'>
					<img
						className='w-full h-48 md:h-64 lg:h-80 object-cover'
						src={`/api/images/${speakersDetails.image}`}
						alt={speakersDetails.name_en || speakersDetails.name_ar}
					/>
				</div>

				{/* Text Content Section */}
				<div className='w-full md:w-2/3 flex flex-col gap-4'>
					<h1
						className={`text-xl sm:text-3xl md:text-4xl font-bold uppercase text-start  ${
							lang === 'ar' ? 'text-right' : 'text-left'
						}`}
					>
						{lang === 'en' ? speakersDetails.name_en : speakersDetails.name_ar}
					</h1>
					<div className='flex justify-between items-center'>
						<p
							className={`text-gray-600 text-base sm:text-lg text-start   flex justify-center gap-1 items-center ${
								lang === 'ar' ? 'text-right' : 'text-left'
							}`}
						>
							<CgWorkAlt className='text-xl' />
							{lang === 'en' ? speakersDetails.job_en : speakersDetails.job_ar}
						</p>
						<p
							className={`text-gray-600 text-base sm:text-lg text-start   flex justify-center gap-1 items-center ${
								lang === 'ar' ? 'text-right' : 'text-left'
							}`}
						>
							{' '}
							<AiOutlineGlobal className='text-xl' />
							{lang === 'en'
								? speakersDetails.country_en
								: speakersDetails.country_ar}
						</p>
					</div>

					<p
						className='text-gray-800 text-sm sm:text-base leading-relaxed text-justify'
						dangerouslySetInnerHTML={{
							__html:
								lang === 'en'
									? speakersDetails.desc_en
									: speakersDetails.desc_ar,
						}}
					></p>
				</div>
			</div>
		</div>
	);
}
