import React, { useContext, useEffect, useState } from 'react';
import Slider from 'react-slick';
import axios from 'axios';
import 'slick-carousel/slick/slick.css';
import 'slick-carousel/slick/slick-theme.css';
import '../../../css/NewsSlider.css';
import LangContext from '@/components/langContext/LangContext.jsx';
import { Link } from 'react-router-dom';

export default function NewsSlider() {
	const [news, setNews] = useState([]);
	const { lang, toggleLang } = useContext(LangContext);
	const stripHtmlTags = (html) => {
		const div = document.createElement('div');
		div.innerHTML = html;
		return div.textContent || div.innerText || '';
	};
	useEffect(() => {
		axios.get('/api/all/news').then((response) => {
			setNews(response.data);
		});
	}, []);
	const handleNewsClick = (newId) => {
		router.push(`/news/${newId}`);
	};
	const settings = {
		dots: true,
		infinite: true,
		speed: 500,
		slidesToShow: 3,
		slidesToScroll: 1,
		autoplay: true,
		autoplaySpeed: 3000,
		responsive: [
			{
				breakpoint: 768,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1,
					dots: true,
				},
			},
		],
	};

	return (
		<div className='overflow-hidden'>
			<Slider {...settings}>
				{news.map((post) => (
					<Link to={`/news/${post.id}`}>
						<div key={post.id} className='p-4 h-80 cursor-pointer'>
							<div className='border rounded shadow-lg overflow-hidden h-full '>
								<img
									className='w-full h-40 object-cover'
									src={`/api/images/${post.image}`}
									alt='News Image'
								/>
								<div className='p-4 flex flex-col justify-between'>
									<h2 className='text-lg font-bold mb-2 '>
										{lang === 'en'
											? stripHtmlTags(post.desc_en.slice(0, 30)) + '...'
											: stripHtmlTags(post.desc_ar.slice(0, 30)) + '...'}
									</h2>
									<p className='text-sm text-gray-600'>{post.event_time}</p>
								</div>
							</div>
						</div>
					</Link>
				))}
			</Slider>
		</div>
	);
}
