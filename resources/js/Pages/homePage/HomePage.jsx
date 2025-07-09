import React, { useContext } from 'react';
import SpeackerSlider from '@/Pages/homePage/SpeackerSlider.jsx';
import videoPlayes from '../../../css/images/POINT12-24-25.mp4';
import homeImageAr from '../../../css/images/point_home_page_ar.jpeg';
import homeImageEn from '../../../css/images/point_home_page_en.jpeg';
import LangContext from '@/components/langContext/LangContext.jsx';
import NewsSlider from '@/Pages/homePage/NewsSlider.jsx';

export default function HomePage() {
	const { lang, toggleLang } = useContext(LangContext);

	return (
		<div className='MainHomePage'>
			{lang === 'en' ? (
				<div
					className='home-video'
					style={{ backgroundImage: `url(${homeImageEn})` }}
				>
					{/* <video className='banner' muted autoPlay loop src={videoPlayes}></video> */}
				</div>
			) : (
				<div
					className='home-video'
					style={{ backgroundImage: `url(${homeImageAr})` }}
				>
					{/* <video className='banner' muted autoPlay loop src={videoPlayes}></video> */}
				</div>
			)}

			<div className='-mt-3'>
				<NewsSlider />
				<h1 className='text-3xl font-bold px-2 text-center uppercase pt-4'>
					{' '}
					{lang === 'en' ? 'Speaker' : 'المتحدثين'}
				</h1>
				<SpeackerSlider />
			</div>
		</div>
	);
}
