import React, { useContext } from 'react';
import SpeackerSlider from '@/Pages/homePage/SpeackerSlider.jsx';
import videoPlayes from '../../../css/images/POINT12-24-25.mp4';
import homeImageAr from '../../../css/images/point7_home_page_ar.png';
import homeImageEn from '../../../css/images/point7_home_page_en.png';
import LangContext from '@/components/langContext/LangContext.jsx';
import NewsSlider from '@/Pages/homePage/NewsSlider.jsx';
// test push
export default function HomePage() {
	const { lang, toggleLang } = useContext(LangContext);
	const homeBackgroundImage = lang === 'en' ? homeImageEn : homeImageAr;

	return (
		<div className='MainHomePage'>
			<div
				className='home-video'
				style={{ backgroundImage: `url(${homeBackgroundImage})` }}
			>
				{/* <video className='banner' muted autoPlay loop src={videoPlayes}></video> */}
			</div>
			{/* {lang === 'en' ? (
				<div
					className='home-video'
					style={{ backgroundImage: `url(${homeImage})` }}
				>
				</div>
			) : (
				<div
					className='home-video'
					style={{ backgroundImage: `url(${homeImage})` }}
				>
				</div>
			)} */}

			<div className=''>
				<NewsSlider />
				<h1 className='text-2xl px-2 text-center uppercase pt-4'>
					{' '}
					{lang === 'en' ? 'Speaker' : 'المتحدثين'}
				</h1>
				<SpeackerSlider />
			</div>
		</div>
	);
}
