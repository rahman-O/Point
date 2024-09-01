import React, {useContext, useEffect, useState} from "react";
import Slider from "react-slick";
import axios from "axios";
import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";
import "../../../css/NewsSlider.css";
import LangContext from "@/components/langContext/LangContext.jsx";

export default function NewsSlider() {
    const [news, setNews] = useState([]);
    const { lang, toggleLang} = useContext(LangContext);

    useEffect(() => {
        axios.get('/api/all/news').then(response => {
            setNews(response.data);
        });
    }, []);

    const settings = {
        dots: true,
        infinite: true,
        speed: 500,
        slidesToShow: 3,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 3000,
    };

    return (
        <div className=" h-80 overflow-hidden relative">
            <Slider {...settings}>
                {news.map((post) => (
                    <div key={post.id} className="p-4 ">
                        <div className="border rounded-lg shadow-lg overflow-hidden">
                            <img
                                className="w-full h-48 object-cover"
                                src={`/api/images/${post.image}`}
                                alt="News Image"
                            />
                            <div className="p-4">
                                <h2 className="text-lg font-bold mb-2">{lang==="en"?  post.desc_en:post.desc_en}</h2>
                                <p className="text-sm text-gray-600">{post.event_time}</p>
                            </div>
                        </div>
                    </div>
                ))}
            </Slider>
        </div>
    );
}
