import React, {useEffect, useState} from "react";

export default function Conferences() {
    const [conference, setConference] = useState(null);

    useEffect(() => {
        fetch('/api/conferences/last')
            .then((response) => response.json())
            .then((data) => setConference(data));
    }, []);

    if (!conference) return <div>Loading...</div>;

    return (
        <div className="w-full grid justify-items-center items-center">
            <div className=" grid gap-4 w-1/2 pt-4">
                <h1 className="text-4xl font-bold uppercase">{conference.title_en}</h1>
                <img className="" src={`/api/images/${conference.image}`}/>
                <p className="text-justify">{conference.desc_en}</p>
            </div>
        </div>
    );
}
