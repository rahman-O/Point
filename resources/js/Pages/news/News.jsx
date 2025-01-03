import React, { useContext, useEffect, useState } from 'react';
import axios from 'axios';
import { CardNews } from '@/Components/CardNews.jsx';
import Pagination from '@/components/Pagination.jsx';
import LangContext from '@/components/langContext/LangContext.jsx';

export default function News() {
	const [news, setNews] = useState([]);
	const [years, setYears] = useState([]);
	const [currentPage, setCurrentPage] = useState(1);
	const [totalPages, setTotalPages] = useState(1);
	const [selectedYear, setSelectedYear] = useState(null);
	const { lang } = useContext(LangContext);
	// Helper function to strip HTML tags
	const stripHtmlTags = (html) => {
		const div = document.createElement('div');
		div.innerHTML = html;
		return div.textContent || div.innerText || '';
	};
	const fetchData = async (page, year) => {
		const response = await axios.get(`/api/news/event`, {
			params: {
				page,
				year,
			},
		});

		if (response) {
			setNews(response.data.news.data);
			setTotalPages(response.data.news.last_page);
			if (!years.length) {
				setYears(response.data.years); // Update available years
			}
		}
	};

	useEffect(() => {
		fetchData(currentPage, selectedYear);
	}, [currentPage, selectedYear]);

	const handlePageChange = (page) => {
		setCurrentPage(page);
	};

	const handleYearChange = (year) => {
		setSelectedYear((prevYear) => (prevYear === year ? null : year));
		setCurrentPage(1);
	};

	return (
		<div className='py-6'>
			<div className='flex justify-center mb-4 gap-2'>
				{years.map((year) => (
					<button
						key={year}
						onClick={() => handleYearChange(year)}
						className={`px-4 py-2 border rounded ${
							selectedYear === year ? 'bg-lime-500 text-white' : 'bg-gray-200'
						}`}
					>
						{year}
					</button>
				))}
			</div>
			<div className='grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 px-4'>
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
					<p>{lang === 'en' ? ' No news available.' : 'لا توجد اخبار بعد'}</p>
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
