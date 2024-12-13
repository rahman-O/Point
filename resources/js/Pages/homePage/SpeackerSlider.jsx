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
			<div className='h-fit overflow-hidden grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 px-4'>
				{speakers.map((speaker) => (
					<Link to={`/speakers/${speaker.id}`}>
						<div key={speaker.id} className='cursor-pointer'>
							<div className='border rounded shadow-lg overflow-hidden'>
								<img
									className='w-full h-48 object-cover'
									src={`/api/images/${speaker.image}`}
									alt='Speaker Image'
								/>
								<div className='p-4'>
									<div className='flex justify-between items-center'>
										<h2 className='text-lg font-bold mb-2'>
											{lang === 'en' ? speaker.name_en : speaker.name_ar}
										</h2>
										<span className='text-right'>
											<CgWorkAlt className='inline pr-2 text-3xl' />
											{lang === 'en'
												? speaker.job_en.slice(0, 20) + '..'
												: speaker.job_ar.slice(0, 20) + '..'}
										</span>
									</div>
									<p className='text-sm text-gray-600'>
										{lang === 'en' ? speaker.country_en : speaker.country_ar}
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
