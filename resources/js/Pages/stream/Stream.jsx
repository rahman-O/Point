import React, { useContext, useEffect, useState } from 'react';
import axios from 'axios';
import Pagination from '@/components/Pagination.jsx';
import LangContext from '@/components/langContext/LangContext.jsx';
import VideoCard from './VideoCard.jsx';

const PER_PAGE = 10;

export default function Stream() {
	const [streams, setStreams] = useState(null);
	const [playingId, setPlayingId] = useState(null);
	const [currentPage, setCurrentPage] = useState(1);
	const [totalPages, setTotalPages] = useState(1);
	const { lang } = useContext(LangContext);

	useEffect(() => {
		let cancelled = false;
		setStreams(null);
		setPlayingId(null);
		axios
			.get(`/api/stream?page=${currentPage}`)
			.then((response) => {
				if (cancelled) return;
				setStreams(response.data.data);
				setTotalPages(response.data.last_page);
			})
			.catch(() => {
				if (!cancelled) setStreams([]);
			});
		return () => {
			cancelled = true;
		};
	}, [currentPage]);

	return (
		<div>
			<div className='grid grid-cols-1 gap-6 px-2 py-2 sm:grid-cols-2 md:grid-cols-3'>
				{streams === null
					? Array.from({ length: PER_PAGE }, (_, i) => (
							<div key={i} className='h-56 w-full animate-pulse rounded-lg bg-gray-200' />
					  ))
					: streams.map((stream) => (
							<VideoCard
								key={stream.id}
								videoId={stream.youtube_video_id}
								title={lang === 'en' ? stream.title_en : stream.title_ar}
								playing={playingId === stream.id}
								onPlay={() => setPlayingId(stream.id)}
								lang={lang}
							/>
					  ))}
			</div>

			<Pagination
				currentPage={currentPage}
				totalPages={totalPages}
				onPageChange={setCurrentPage}
			/>
		</div>
	);
}
