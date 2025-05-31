import React, { useContext, useEffect, useState } from 'react';
import axios from 'axios';
import { CardSpeaker } from '@/components/CardSpeaker.jsx';
import Pagination from '@/components/Pagination.jsx';
import LangContext from '@/components/langContext/LangContext.jsx';

export default function Speakers() {
	const [speakers, setSpeakers] = useState([]);
	const [years, setYears] = useState([]);
	const [currentPage, setCurrentPage] = useState(1);
	const [totalPages, setTotalPages] = useState(1);
	const [selectedYear, setSelectedYear] = useState(null);
	const { lang } = useContext(LangContext);

	const fetchData = async (page, year) => {
		const response = await axios.get(`/api/speakers`, {
			params: {
				page,
				year,
			},
		});

		if (response) {
			setSpeakers(response.data.speakers.data);
			setTotalPages(response.data.speakers.last_page);
			if (!years.length) {
				setYears(response.data.years);
			}
		}
	};

	useEffect(() => {
		const url = new URL(window.location);
		const page = url.searchParams.get('page') || 1;
		const year = url.searchParams.get('year') || null;

		setCurrentPage(Number(page));
		setSelectedYear(year);
		fetchData(Number(page), year);
	}, []);

	const handlePageChange = (page) => {
		setCurrentPage(page);
		const url = new URL(window.location);
		url.searchParams.set('page', page);
		if (selectedYear) {
			url.searchParams.set('year', selectedYear);
		}
		window.history.pushState({}, '', url);
		fetchData(page, selectedYear);
	};

	const handleYearChange = (year) => {
		const newYear = selectedYear === year ? null : year;
		setSelectedYear(newYear);
		setCurrentPage(1);

		const url = new URL(window.location);
		url.searchParams.set('page', 1);
		if (newYear) {
			url.searchParams.set('year', newYear);
		} else {
			url.searchParams.delete('year');
		}
		window.history.pushState({}, '', url);
		fetchData(1, newYear);
	};

	return (
		<div className='py-6'>
			<div className='flex justify-center flex-wrap mb-4 gap-2'>
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
			<div className='grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 gap-4 px-4'>
				{speakers.map((speaker) => (
					<CardSpeaker
						key={speaker.id}
						id={speaker.id}
						name={lang === 'en' ? speaker.name_en : speaker.name_ar}
						job={lang === 'en' ? speaker.job_en : speaker.job_ar}
						image={speaker.image}
					/>
				))}
			</div>
			{speakers.length > 0 ? (
				<Pagination
					currentPage={currentPage}
					totalPages={totalPages}
					onPageChange={handlePageChange}
				/>
			) : (
				<div className=' h-40 flex justify-center items-center text-xl'>
					<p>
						{lang === 'en' ? ' No speakers available.' : 'لا يوجد متحدثين بعد'}
					</p>
				</div>
			)}
		</div>
	);
}
