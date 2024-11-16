import React, { useContext, useEffect, useState } from 'react';
import axios from 'axios';
import Pagination from '@/Components/Pagination.jsx';
import LangContext from '@/components/langContext/LangContext.jsx';

export default function Stream() {
	const [streams, setStreams] = useState([]);
	const [selectedVideoId, setSelectedVideoId] = useState(null);
	const [currentPage, setCurrentPage] = useState(1);
	const [totalPages, setTotalPages] = useState(1);
	const { lang } = useContext(LangContext);

	const fetchData = async (page) => {
		const response = await axios.get(`/api/stream?page=${page}`);
		if (response) {
			setStreams(response.data.data);
			setTotalPages(response.data.last_page);
		}
	};

	useEffect(() => {
		fetchData(currentPage);
	}, [currentPage]);

	const handlePageChange = (page) => {
		setCurrentPage(page);
	};

	const handleVideoClick = (videoId) => {
		setSelectedVideoId(videoId);
	};

	return (
		<div>
			<div className='grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-3 gap-6 px-2 py-2'>
				{streams.map((stream) => (
					<div
						className='relative overflow-hidden rounded'
						key={stream.id}
						style={{ cursor: 'pointer' }}
						onClick={() => handleVideoClick(stream.youtube_video_id)}
					>
						{selectedVideoId === stream.youtube_video_id ? (
							<iframe
								style={{ borderRadius: '8px' }}
								src={`https://www.youtube.com/embed/${stream.youtube_video_id}?autoplay=1`}
								frameBorder='0'
								allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture'
								allowFullScreen
								title={lang === 'en' ? stream.title_en : stream.title_ar}
							></iframe>
						) : (
							<>
								<img
									src={`https://img.youtube.com/vi/${stream.youtube_video_id}/hqdefault.jpg`}
									alt={lang === 'en' ? stream.title_en : stream.title_ar}
									className='  object-cover'
								/>
								{/* Play Button Overlay */}
								<div className='absolute inset-0 flex justify-center items-center bg-black bg-opacity-20'>
									<img
										src='https://upload.wikimedia.org/wikipedia/commons/7/75/YouTube_social_white_squircle_%282017%29.svg'
										alt='Play Button'
										className='w-16 h-16'
									/>
								</div>
								{/* Title Overlay */}
								<div className='absolute top-0 left-0 w-full px-3 py-2 bg-black bg-opacity-60 text-white text-sm font-bold'>
									{lang === 'en' ? stream.title_en : stream.title_ar}
								</div>
							</>
						)}
					</div>
				))}
			</div>

			<Pagination
				currentPage={currentPage}
				totalPages={totalPages}
				onPageChange={handlePageChange}
			/>
		</div>
	);
}
