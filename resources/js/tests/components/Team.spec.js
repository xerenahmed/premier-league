import {mount} from "@vue/test-utils";
import Team from "@/components/Team.vue";
import {describe, it, expect} from "vitest";

describe("Team.vue", () => {
    it("can render the logo", () => {
        const wrapper = mount(Team, {
            props: {
                team: {
                    flag_url: '/img/man-city-logo.png',
                    name: 'Man City'
                }
            },
        });

        expect(wrapper.text()).toBe('Man City');
    });
});
