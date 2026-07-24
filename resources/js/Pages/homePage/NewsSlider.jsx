import React, { useContext, useEffect, useRef, useState } from 'react';
import Slider from 'react-slick';
import axios from 'axios';
import 'slick-carousel/slick/slick.css';
import 'slick-carousel/slick/slick-theme.css';
import '../../../css/NewsSlider.css';
import LangContext from '@/components/langContext/LangContext.jsx';
import { Link } from 'react-router-dom';
import { FiChevronLeft, FiChevronRight } from 'react-icons/fi';

function PrevArrow({ className, onClick }) {
	return (
		<button
			type='button'
			className={`${className} news-arrow`}
			onClick={onClick}
			aria-label='Previous'
		>
			<FiChevronLeft />
		</button>
	);
}

function NextArrow({ className, onClick }) {
	return (
		<button
			type='button'
			className={`${className} news-arrow`}
			onClick={onClick}
			aria-label='Next'
		>
			<FiChevronRight />
		</button>
	);
}

export default function NewsSlider() {
	const [news, setNews] = useState([]);
	const sliderRef = useRef(null);
	const squareRef = useRef(null);
	const { lang } = useContext(LangContext);
	const isRtl = lang === 'ar';

	useEffect(() => {
		axios.get('/api/all/news').then((response) => {
			setNews(response.data);
		});
	}, []);

	const total = news.length;

	// Position the orange square along the track for a given slide index.
	// Done via a DOM ref (not React state) so the slider itself never re-renders
	// — re-rendering would recreate the arrows and reset slick to the first slide.
	const moveSquare = (index) => {
		if (!squareRef.current) return;
		const frac = total > 1 ? index / (total - 1) : 0;
		const value = `calc(${frac} * (100% - 26px))`;
		if (isRtl) {
			squareRef.current.style.right = value;
			squareRef.current.style.left = 'auto';
		} else {
			squareRef.current.style.left = value;
			squareRef.current.style.right = 'auto';
		}
	};

	const settings = {
		dots: false,
		infinite: true,
		speed: 600,
		slidesToShow: 4,
		slidesToScroll: 1,
		autoplay: true,
		autoplaySpeed: 4000,
		rtl: isRtl,
		arrows: true,
		prevArrow: <PrevArrow />,
		nextArrow: <NextArrow />,
		beforeChange: (oldIndex, newIndex) => {
			if (!total) return;
			moveSquare(((newIndex % total) + total) % total);
		},
		responsive: [
			{ breakpoint: 1280, settings: { slidesToShow: 4 } },
			{ breakpoint: 1024, settings: { slidesToShow: 3 } },
			{ breakpoint: 768, settings: { slidesToShow: 2 } },
			{ breakpoint: 520, settings: { slidesToShow: 1 } },
		],
	};

	// The slider resets to the first slide when the data loads or the language
	// (direction) changes, so keep the square in sync with that.
	useEffect(() => {
		moveSquare(0);
		// eslint-disable-next-line react-hooks/exhaustive-deps
	}, [lang, total]);

	// Clicking anywhere on the track seeks to the matching slide (and activates it).
	const handleTrackClick = (event) => {
		if (total <= 1 || !sliderRef.current) return;
		const rect = event.currentTarget.getBoundingClientRect();
		let frac = (event.clientX - rect.left) / rect.width;
		frac = Math.min(1, Math.max(0, frac));
		// In RTL the track starts (slide 0) from the right edge.
		if (isRtl) frac = 1 - frac;
		const index = Math.round(frac * (total - 1));
		sliderRef.current.slickGoTo(index);
	};

	const squareStyle = isRtl
		? { right: 0, left: 'auto' }
		: { left: 0, right: 'auto' };

	return (
		<div className={`news-slider ${isRtl ? 'text-rtl' : 'text-ltr'}`}>
			<Slider ref={sliderRef} {...settings}>
				{news.map((post) => (
					<div key={post.id} className='news-slide'>
						<Link to={`/news/${post.id}`} className='news-slide-image-wrap'>
							<img
								className='news-slide-image'
								src={`/api/images/${post.image}`}
								alt='News'
							/>
						</Link>
						<div className='news-slide-content'>
							<p className='news-slide-date'>{post.event_time}</p>
							<Link to={`/news/${post.id}`}>
								<h2 className='news-slide-title' dir={isRtl ? 'rtl' : 'ltr'}>
									{lang === 'en' ? post.title_en : post.title_ar}
								</h2>
							</Link>
						</div>
					</div>
				))}
			</Slider>

			{total > 0 && (
				<div className='news-pagination'>
					<div
						className='news-pagination-track'
						onClick={handleTrackClick}
						role='button'
						tabIndex={0}
						aria-label='Go to news slide'
					>
						<span
							ref={squareRef}
							className='news-pagination-square'
							style={squareStyle}
						></span>
					</div>
				</div>
			)}
		</div>
	);
}
