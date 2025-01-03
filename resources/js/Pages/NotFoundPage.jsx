import React, { useContext } from 'react';
import pageNotFound from '../../css/images/page-not-found.svg';
import LangContext from '@/components/langContext/LangContext.jsx';

export default function NotFoundPage() {
	const { lang, toggleLang } = useContext(LangContext);

	return (
		<div className=' w-full h-full flex flex-col justify-center items-center'>
			<div className='w-3/4 h-3/4 flex flex-col items-center'>
				<div
					className='h-full w-full bg-contain bg-no-repeat bg-center '
					style={{ backgroundImage: `url(${pageNotFound})` }}
				></div>
				<div className='my-4 text-3xl'>
					{lang === 'en' ? 'Page Not Found' : 'الصفحة غير موجودة'}{' '}
				</div>
			</div>
		</div>
	);
}
