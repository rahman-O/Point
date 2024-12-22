import React, { useContext, useEffect, useState } from 'react';
import axios from 'axios';
import Pagination from '@/Components/Pagination.jsx';
import { CardNews } from '@/Components/CardNews.jsx';
import LangContext from '@/components/langContext/LangContext.jsx';

export default function News() {
	const [news, setNews] = useState([]); // Ensure this matches the API response type
	const [currentPage, setCurrentPage] = useState(1);
	const [totalPages, setTotalPages] = useState(1);
	const { lang } = useContext(LangContext);

	// Helper function to strip HTML tags
	const stripHtmlTags = (html) => {
		const div = document.createElement('div');
		div.innerHTML = html;
		return div.textContent || div.innerText || '';
	};

	// Fetch data from API
	const fetchData = async (page) => {
		const response = await axios.get(`/api/all-news?page=${page}`);
		if (response && response.data.data) {
			setNews(response.data.data);
			setTotalPages(response.data.last_page);
		}
	};

	// Effect to fetch data when the page changes
	useEffect(() => {
		fetchData(currentPage);
	}, [currentPage]);

	const handlePageChange = (page) => {
		setCurrentPage(page);
	};

	// Render Component
	return (
		<div className='py-6'>
			<div className='grid grid-cols-1 md:grid-cols-5 sm:grid-cols-3 gap-3 px-2'>
				{news.length > 0 ? (
					news.map((post) => (
						<CardNews
							key={post.id}
							id={post.id}
							title={
								lang === 'en'
									? 'point iraq: ' +
									  (post.title_en
											? stripHtmlTags(post.title_en).slice(0, 40) +
											  (post.title_en.length > 40 ? '...' : '')
											: '')
									: 'بوينت العراق : ' +
									  (post.title_ar
											? stripHtmlTags(post.title_ar).slice(0, 40) +
											  (post.title_ar.length > 40 ? '...' : '')
											: '')
							}
							author={lang === 'en' ? post.author_en : post.author_ar}
							event_time={post.event_time}
							image={post.image}
						/>
					))
				) : (
					<p>No news available.</p>
				)}
			</div>
			<Pagination
				currentPage={currentPage}
				totalPages={totalPages}
				onPageChange={handlePageChange}
			/>
		</div>
	);
}
