import axios from 'axios';
const query = process.argv[2] || 'frock';

async function scrape() {
    try {
        const { data } = await axios.get(`https://www.daraz.pk/catalog/?q=${encodeURIComponent(query)}&ajax=true`, {
            headers: { 'User-Agent': 'Mozilla/5.0' }
        });

        const items = data.mods?.listItems?.map(item => ({
            name: item.name || "No Name",
            priceShow: item.priceShow || "0",
            image: item.image || "",
            link: item.itemUrl ? (item.itemUrl.startsWith('//') ? `https:${item.itemUrl}` : item.itemUrl) : '#'
        })) || [];

        console.log(JSON.stringify(items));
    } catch (e) {
        console.log(JSON.stringify([]));
    }
}
scrape();