import React, { useContext, useEffect, useState } from 'react';
import axios from 'axios';
import { CardSpeaker } from '@/components/CardSpeaker.jsx';
import Pagination from '@/components/Pagination.jsx';
import YearFilter from '@/components/YearFilter.jsx';
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

	// Loads the year list, then selects the most recent year by default
	// (used when the page is opened without a ?year= in the URL).
	const loadDefaultYear = async (page) => {
		const response = await axios.get(`/api/speakers`, { params: { page } });
		if (!response) return;

		const yrs = response.data.years || [];
		setYears(yrs);

		if (yrs.length) {
			const latestYear = yrs[0]; // API returns years ordered newest first
			setSelectedYear(latestYear);

			const url = new URL(window.location);
			url.searchParams.set('year', latestYear);
			window.history.replaceState({}, '', url);

			fetchData(page, latestYear);
		} else {
			setSpeakers(response.data.speakers.data);
			setTotalPages(response.data.speakers.last_page);
		}
	};

	useEffect(() => {
		const url = new URL(window.location);
		const page = Number(url.searchParams.get('page') || 1);
		const yearParam = url.searchParams.get('year');

		setCurrentPage(page);

		if (yearParam) {
			setSelectedYear(yearParam);
			fetchData(page, yearParam);
		} else {
			// No year in the URL: default to the most recent year.
			loadDefaultYear(page);
		}
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
		const newYear = String(selectedYear) === String(year) ? null : year;
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
			<YearFilter
				years={years}
				selectedYear={selectedYear}
				onSelect={handleYearChange}
			/>
			<div className='grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-5 gap-3 px-4'>
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
