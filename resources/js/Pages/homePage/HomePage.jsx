import React, { useContext } from 'react';
import SpeackerSlider from '@/Pages/homePage/SpeackerSlider.jsx';
import videoPlayes from '../../../css/images/POINT12-24-25.mp4';
import homeImage from '../../../css/images/point-za-sada.df9c31bdb1d7be8e.jpeg';
import LangContext from '@/components/langContext/LangContext.jsx';
import NewsSlider from '@/Pages/homePage/NewsSlider.jsx';

export default function HomePage() {
	const { lang, toggleLang } = useContext(LangContext);

	return (
		<div className='MainHomePage'>
			<div
				className='home-video'
				style={{ backgroundImage: `url(${homeImage})` }}
			>
				<video className='banner' muted autoPlay loop src={videoPlayes}></video>
			</div>
			<div className='grid gap-6'>
				<h1 className='text-2xl font-bold px-2 text-center uppercase pt-6'>
					{lang === 'en' ? 'News' : 'الاخبار'}{' '}
				</h1>
				<NewsSlider />
				<h1 className='text-2xl font-bold px-2 text-center uppercase'>
					{' '}
					{lang === 'en' ? 'Speaker' : 'الاجندة'}{' '}
				</h1>
				<SpeackerSlider />
			</div>
		</div>
	);
}
