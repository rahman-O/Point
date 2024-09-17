import React, {useContext, useEffect, useState} from "react";
import {TapsSessions} from "./componets/TapsSessions";
import axios from "axios";
import LangContext from "@/components/langContext/LangContext.jsx";
import {SpeakerList} from "@/Pages/programs/componets/SpeakerList.jsx";


export default function Programs() {

    const [porgrams, setProgram] = React.useState([]);
    const {lang, toggleLang} = useContext(LangContext);

    const [activeTab, setActiveTab] = useState(1);

    useEffect(() => {

        axios.get('/api/programs')
            .then(response => {

                setProgram(response.data.data);
            })
            .catch(error => {
                console.log(error);
            });

    }, []);

    const isValidIndex = activeTab >= 0;
    const currentProgram = porgrams.find(program => program.id === activeTab);


    const sessionsProgram3 = currentProgram?.sessionsProgram.map((session) => (
        <div>
            {/*className="w-full text-center md:m-5 md:ml-[54] my-6 flex md:flex-row flex-col align-items-center"*/}
            <div className="flex align-items-center pt-6">

            <span
                className="text-lg px-4 ">{session.start_time} - {session.end_time}
            </span>

                <span
                    className=" text-lg font-bold">{session.name.toUpperCase()}
                </span>
            </div>


            <div className="pl-32 py-2">
                <SpeakerList session={session}/>
            </div>
        </div>


    ));

    return (
        <div className="mb-[100]">
            <h1 className="text-2xl text-center mt-12 uppercase">Programs</h1>
            <TapsSessions programsArray={porgrams} activeTab={activeTab} setActiveTab={setActiveTab}/>

            {currentProgram ? (
                <div className="w-full text-center">{sessionsProgram3}</div>
            ) : (
                <p>No program selected or invalid tab index</p>
            )}
        </div>
    );
}
