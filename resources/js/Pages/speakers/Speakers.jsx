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
