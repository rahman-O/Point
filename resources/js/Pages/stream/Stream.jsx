import React, {useEffect, useState} from "react";
import axios from "axios";
import Pagination from "@/Components/Pagination.jsx";

export default function Stream() {
    const [streams, setStreams] = useState([]);
    const [selectedVideoId, setSelectedVideoId] = useState(null);
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);

    const fetchData = async (page) => {
        const response = await axios.get(`/api/stream?page=${page}`);
        if (response) {
            setStreams(response.data.data);
            setTotalPages(response.data.last_page);
        }
    };

    useEffect(() => {
        fetchData(currentPage);
    }, [currentPage]);

    const handleNextPage = () => {
        if (currentPage < totalPages) {
            setCurrentPage(currentPage + 1);
        }
    };

    const handlePrevPage = () => {
        if (currentPage > 1) {
            setCurrentPage(currentPage - 1);
        }
    };

    const handlePageChange = (page) => {
        setCurrentPage(page);
    };

    const handleVideoClick = (videoId) => {
        setSelectedVideoId(videoId);
    };

    return (
        <div>
            <div className="grid grid-cols-1 md:grid-cols-5 sm:grid-cols-3 gap-3 px-2 py-2 ">
                {streams.map((stream) => (
                    <div
                        className="pb-8 grid gap-2"
                        key={stream.id}
                        style={{margin: "2px", cursor: "pointer",}}
                    >
                        {selectedVideoId === stream.youtube_video_id ? (
                            <iframe
                                style={{width: "100%", borderRadius: "8px", height: "100%"}}
                                src={`https://www.youtube.com/embed/${stream.youtube_video_id}?autoplay=1`}
                                frameBorder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowFullScreen
                                title={stream.title_en}
                            ></iframe>
                        ) : (
                            <img
                                src={`https://img.youtube.com/vi/${stream.youtube_video_id}/hqdefault.jpg`}
                                alt={stream.title_en}
                                style={{width: "100%", borderRadius: "8px", height: "100%"}}
                                onClick={() => handleVideoClick(stream.youtube_video_id)}
                            />
                        )}
                        <h3 className="font-bold text-2xl uppercase">{stream.title_en}</h3>
                    </div>
                ))}
            </div>

            <Pagination
                currentPage={currentPage}
                totalPages={totalPages}
                onPageChange={handlePageChange}
            />
        </div>
    );
}
