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
				setYears(response.data.years);
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
		<div className='py-6 px-2'>
			<div className='flex justify-center mb-4 gap-2'>
				{news.length > 0
					? years.map((year) => (
							<button
								key={year}
								onClick={() => handleYearChange(year)}
								className={`px-4 py-2 border rounded ${
									selectedYear === year
										? 'bg-lime-500 text-white'
										: 'bg-gray-200'
								}`}
							>
								{year}
							</button>
					  ))
					: ''}
			</div>
			<div className='grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6 px-4'>
				{news.length > 0
					? news.map((post) => (
							<CardNews
								key={post.id}
								id={post.id}
								title={
									lang === 'en'
										? 'point iraq: ' + stripHtmlTags(post.title_en)
										: 'بوينت العراق : ' + stripHtmlTags(post.title_ar)
								}
								author={lang === 'en' ? post.author_en : post.author_ar}
								event_time={post.event_time}
								image={post.image}
							/>
					  ))
					: ''}
			</div>
			{news.length > 0 ? (
				<Pagination
					currentPage={currentPage}
					totalPages={totalPages}
					onPageChange={handlePageChange}
				/>
			) : (
				<div className=' h-40 flex justify-center items-center text-xl'>
					<p>{lang === 'en' ? ' No news available.' : 'لا توجد اخبار بعد'}</p>
				</div>
			)}
		</div>
	);
}
