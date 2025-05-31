import { Link } from 'react-router-dom';
const stripHtmlTags = (content) => {
	const div = document.createElement('div');
	div.innerHTML = content;
	return div.textContent || div.innerText || '';
};
export const CardSpeaker = ({ id, name, job, image, desc }) => {
	return (
		<Link to={`/speakers/${id}`}>
			<div className=' rounded-none p-1'>
				<div className='overflow-visible p-0'>
					<img
						width='100%'
						className='w-full h-[220px] object-cover rounded-md'
						src={`/api/images/${image}`}
					/>
				</div>
				<div className='text-xl grid '>
					<b>{name}</b>
					{/* <p className='text-gray-500'>{job}</p>
					<p className='text-gray-500'>{desc}</p> */}
				</div>
			</div>
		</Link>
	);
};
