import React, { useContext } from 'react';
import SpeackerSlider from '@/Pages/homePage/SpeackerSlider.jsx';
import videoPlayes from '../../../css/images/POINT12-24-25.mp4';
import homeImage from '../../../css/images/point7_home_page.png';
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
