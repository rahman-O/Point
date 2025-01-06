import React, { useContext, useEffect, useState } from 'react';
import axios from 'axios';
import { CgWorkAlt } from 'react-icons/cg';
import LangContext from '@/components/langContext/LangContext.jsx';
import { Link } from 'react-router-dom';

export default function NewsSlider() {
	const [speakers, setSpeakers] = useState([]);
	const [currentPage, setCurrentPage] = useState(1);
	const [totalPages, setTotalPages] = useState(1);
	const { lang } = useContext(LangContext);

	const fetchData = async (page) => {
		try {
			const response = await axios.get(`/api/speakers?page=${page}`);
			const speakersData = response.data.speakers;
			if (speakersData) {
				setSpeakers((prevSpeakers) => [...prevSpeakers, ...speakersData.data]);
				setTotalPages(speakersData.last_page);
			}
		} catch (error) {
			return error;
		}
	};
	const handleSpeakerClick = (speakerId) => {
		router.push(`/speakers/${speakerId}`);
	};
	useEffect(() => {
		fetchData(currentPage);
	}, []);

	const handleLoadMore = () => {
		if (currentPage < totalPages) {
			const nextPage = currentPage + 1;
			setCurrentPage(nextPage);
			fetchData(nextPage);
		}
	};

	return (
		<div>
			<div className='h-fit overflow-hidden grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 px-4 my-8'>
				{speakers.map((speaker) => (
					<Link to={`/speakers/${speaker.id}`}>
						<div key={speaker.id} className='cursor-pointer'>
							<div className='overflow-visible p-0'>
								<img
									className='w-full h-48 object-cover'
									src={`/api/images/${speaker.image}`}
									alt='Speaker Image'
								/>
								<div className='pt-2'>
									<p className='text-xl font-bold'>
										{lang === 'en' ? speaker.name_en : speaker.name_ar}
									</p>
									<p className='text-lg'>
										{lang === 'en' ? speaker.job_en : speaker.job_ar}
									</p>
								</div>
							</div>
						</div>
					</Link>
				))}
			</div>
			{currentPage < totalPages && (
				<div className='flex justify-center mt-4'>
					<button
						onClick={handleLoadMore}
						className='bg-lime-500 text-white font-bold py-2 px-4 rounded hover:bg-lime-700'
					>
						{lang === 'en' ? 'Load More' : 'تحميل المزيد'}
					</button>
				</div>
			)}
		</div>
	);
}
