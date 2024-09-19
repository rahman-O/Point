import React, { useContext, useEffect, useState } from 'react';
import axios from 'axios';
import Pagination from '@/Components/Pagination.jsx';
import { CardNews } from '@/Components/CardNews.jsx';
import LangContext from '@/components/langContext/LangContext.jsx';

export default function News() {
	const [news, setNews] = useState([]);
	const [currentPage, setCurrentPage] = useState(1);
	const [totalPages, setTotalPages] = useState(1);
	const { lang, toggleLang } = useContext(LangContext);
	const stripHtmlTags = (html) => {
		const div = document.createElement('div');
		div.innerHTML = html;
		return div.textContent || div.innerText || '';
	};
	const fetchData = async (page) => {
		const response = await axios.get(`/api/news?page=${page}`);
		if (response) {
			setNews(response.data.data);
			setTotalPages(response.data.last_page);
		}
	};

	useEffect(() => {
		fetchData(currentPage);
	}, [currentPage]);

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
			<div className='grid grid-cols-1 md:grid-cols-5 sm:grid-cols-3 gap-3 px-2 '>
				{news.map((post) => (
					<CardNews
						key={post.id}
						id={post.id}
						desc_en={
							lang === 'en'
								? stripHtmlTags(post.desc_en.slice(0, 40)) + '...'
								: stripHtmlTags(post.desc_ar.slice(0, 40)) + '...'
						}
						event_time={post.event_time}
						image={post.image}
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
