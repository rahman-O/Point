import React from "react";
import { Tabs } from "flowbite-react";

export function TapsSessions() {
    return (
      <Tabs aria-label="Pills" variant="pills">
        <Tabs.Item active title="Tab 1">
          <p className="text-sm text-gray-500 dark:text-gray-400">Content 1</p>
        </Tabs.Item>
        <Tabs.Item title="Tab 2">
          <p className="text-sm text-gray-500 dark:text-gray-400">Content 2</p>
        </Tabs.Item>
        <Tabs.Item title="Tab 3">
          <p className="text-sm text-gray-500 dark:text-gray-400">Content 3</p>
        </Tabs.Item>
        <Tabs.Item title="Tab 4">
          <p className="text-sm text-gray-500 dark:text-gray-400">Content 4</p>
        </Tabs.Item>
        <Tabs.Item disabled title="Tab 5">
          <p className="text-sm text-gray-500 dark:text-gray-400">Content 5</p>
        </Tabs.Item>
      </Tabs>
    );
  }