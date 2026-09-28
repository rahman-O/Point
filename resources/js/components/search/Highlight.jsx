import React from 'react';

// Renders server-provided [{text, match}] segments; React escapes the text, so no HTML is injected.
export default function Highlight({ segments, className = '' }) {
	if (!segments?.length) return null;

	return (
		<span className={className}>
			{segments.map((segment, i) =>
				segment.match ? (
					<mark
						key={i}
						className='rounded-sm bg-point-orange/25 px-0.5 font-semibold text-inherit'
					>
						{segment.text}
					</mark>
				) : (
					<React.Fragment key={i}>{segment.text}</React.Fragment>
				),
			)}
		</span>
	);
}
