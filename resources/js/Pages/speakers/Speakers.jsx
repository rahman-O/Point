import React, { useContext, useEffect, useState } from 'react';
import axios from 'axios';
import { CardSpeaker } from '@/components/CardSpeaker.jsx';
import Pagination from '@/components/Pagination.jsx';
import LangContext from '@/components/langContext/LangContext.jsx';

export default function Speakers() {
	const [speakers, setSpeakers] = useState([]);
	const [currentPage, setCurrentPage] = useState(1);
	const [totalPages, setTotalPages] = useState(1);
	const { lang, toggleLang } = useContext(LangContext);
	const fetchData = async (page) => {
		const response = await axios.get(`/api/speakers?page=${page}`);
		if (response) {
			setSpeakers(response.data.data);
			setTotalPages(response.data.last_page);
		}
	};

	useEffect(() => {
		fetchData(currentPage);
	}, [currentPage]);
	const stripHtmlTags = (content) => {
		const div = document.createElement('div');
		div.innerHTML = content;
		return div.textContent || div.innerText || '';
	};
	const handleNextPage = () => {
		if (currentPage < totalPages) {
			setCurrentPage(currentPage + 1);
		}
	};

	const handlePrevPage = () => {
		if (currentPage > 1) {
			setCurrentPage(currentPage - 1);
		}
	};
	const handlePageChange = (page) => {
		setCurrentPage(page);
	};
	return (
		<div className='py-6'>
			<div className='grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 px-4'>
				{speakers.map((speaker) => (
					<CardSpeaker
						key={speaker.id}
						id={speaker.id}
						name={lang === 'en' ? speaker.name_en : speaker.name_ar}
						job_en={lang === 'en' ? speaker.job_en : speaker.job_ar}
						desc={
							lang === 'en'
								? stripHtmlTags(speaker.desc_en.slice(0, 40) + '...')
								: stripHtmlTags(speaker.desc_ar.slice(0, 40) + '...')
						}
						image={speaker.image}
					/>
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
