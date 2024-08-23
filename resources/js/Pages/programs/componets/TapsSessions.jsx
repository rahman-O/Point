
import { Tabs } from "flowbite-react";

export function TapsSessions({programsArray  , activeTab, setActiveTab}) {

    return (
        <div className="flex flex-row w-full mt-[60] justify-center ">
            {programsArray.map((program) => (
                <div className="gap-4 mr-3 ">
                    <Tabs aria-label={program.id} variant={activeTab===program.id?"pills":"default"}  onActiveTabChange={(tab) => setActiveTab(program.id)}  >
                        <Tabs.Item  active="off" title={program.year}>

                        </Tabs.Item>
                    </Tabs>
                </div>

            ))}
        </div>
    );
  }
