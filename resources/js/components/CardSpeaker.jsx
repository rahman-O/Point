import { Link } from 'react-router-dom';
const stripHtmlTags = (content) => {
	const div = document.createElement('div');
	div.innerHTML = content;
	return div.textContent || div.innerText || '';
};
export const CardSpeaker = ({ id, name, job, image, desc }) => {
	return (
		<Link to={`/speakers/${id}`}>
			<div className='p-1'>
				<div className='overflow-visible p-0'>
					<img
						width='100%'
						className='w-full h-[220px] object-cover'
						src={`/api/images/${image}`}
					/>
				</div>
				<div className='text-xl grid mt-4 '>
					<b>{name}</b>
					{job && <p className='text-sm text-gray-500'>{job}</p>}
				</div>
			</div>
		</Link>
	);
};
