import React, { useContext, useEffect, useState } from 'react';
import axios from 'axios';
import { CgWorkAlt } from 'react-icons/cg';
import LangContext from '@/components/langContext/LangContext.jsx';
import { Link } from 'react-router-dom';

export default function SpeackerSlider() {
	const [speakers, setSpeakers] = useState([]);
	const [currentPage, setCurrentPage] = useState(1);
	const { lang } = useContext(LangContext);

	const fetchData = async (page) => {
		try {
			const response = await axios.get('/api/top/speakers');
			const speakersData = response.data.speakers;

			if (Array.isArray(speakersData)) {
				setSpeakers((prevSpeakers) => [...prevSpeakers, ...speakersData]);
			}
		} catch (error) {
			console.error('Fetch error:', error);
		}
	};
	const handleSpeakerClick = (speakerId) => {
		router.push(`/speakers/${speakerId}`);
	};
	useEffect(() => {
		fetchData(currentPage);
	}, []);

	return (
		<div>
			<div className='h-fit overflow-hidden grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 px-2 my-8'>
				{speakers.map((speaker) => (
					<Link to={`/speakers/${speaker.id}`}>
						<div key={speaker.id} className='cursor-pointer'>
							<div className='overflow-visible p-0'>
								<img
									className='w-full h-48 object-cover rounded-md'
									src={`/api/images/${speaker.image}`}
									alt='Speaker Image'
								/>
								<div className='pt-2'>
									<p className='text-base font-bold'>
										{lang === 'en' ? speaker.name_en : speaker.name_ar}
									</p>
									<p className='text-xs'>
										{lang === 'en' ? speaker.job_en : speaker.job_ar}
									</p>
								</div>
							</div>
						</div>
					</Link>
				))}
			</div>
		</div>
	);
}
