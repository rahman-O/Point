import React from 'react';

export default function Loader() {
	return (
		<div className='fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50'>
			<div className='loader border-t-4 border-b-4 border-white rounded-full w-24 h-24 animate-spin'></div>
		</div>
	);
}
