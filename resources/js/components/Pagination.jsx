import React, { useContext } from 'react';
import LangContext from '@/components/langContext/LangContext.jsx';

//  its a test commit
//  its a second test commit
const Pagination = ({ currentPage, totalPages, onPageChange }) => {
	const { lang } = useContext(LangContext);

	return (
		<div className='flex justify-center mt-4'>
			<button
				onClick={() => onPageChange(currentPage - 1)}
				disabled={currentPage === 1}
				className='px-4 py-2 mr-2 bg-gray-200 rounded disabled:opacity-50'
			>
				{lang === 'en' ? 'Previous' : 'السابق'}
			</button>
			<span className='px-4 py-2'>
				{lang === 'en'
					? `Page ${currentPage} of ${totalPages}`
					: `صفحة ${currentPage} من ${totalPages}`}
			</span>
			<button
				onClick={() => onPageChange(currentPage + 1)}
				disabled={currentPage === totalPages}
				className='px-4 py-2 bg-gray-200 rounded disabled:opacity-50'
			>
				{lang === 'en' ? 'Next' : 'التالي'}
			</button>
		</div>
	);
};

export default Pagination;
