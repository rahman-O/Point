import React from "react";
import NewsSlider from "@/Pages/homePage/NewsSlider.jsx";
import SpeackerSlider from "@/Pages/homePage/SpeackerSlider.jsx";
import videoPlayes from "../../../css/images/POINT12-24-25.mp4";
import homeImage from '../../../css/images/point-za-sada.df9c31bdb1d7be8e.jpeg'; /* Adjust the path */

export default function HomePage() {
    return (
        <div className="">
            <div className="home-video" style={{backgroundImage: `url(${homeImage})`}}>
                <video className="banner" muted autoPlay loop src={videoPlayes}></video>
            </div>
            <div className="grid gap-6">
                <NewsSlider/>
                <h1 className="text-2xl font-bold px-2 text-center uppercase">Speakers</h1>
                <SpeackerSlider/>
            </div>
        </div>
    );
}
