export const colors = {
  COLOR_PRIMARY: "#f96163",
  COLOR_LIGHT: "#fff",
  COLOR_DARK: "#000",
  COLOR_DARK_ALT: "#262626",
};

// Registered Users
export const registeredUsers = [
  {
    id: 1,
    username: "Edrick",
    password: "Yesorno@12",
    phoneNumber: "0770444853",
  },

  {
    id: 2,
    username: "Remick",
    password: "Nooryes@12",
    phoneNumber: "0709605076",
  },

  {
    id: 3,
    username: "Agnes",
    password: "Aggie#06",
    phoneNumber: "0758496558",
  },
];

// export function getUsers() {
//   return registeredUsers;
// }

export function registerUser(username, password, phoneNumber) {
  const newUser = { username, password, phoneNumber };
  registeredUsers.push(newUser);
}

// Food Eating time Categories
export const categories = [
  {
    id: "01",
    category: "BreakFast",
  },

  {
    id: "02",
    category: "Lunch",
  },

  {
    id: "03",
    category: "Dinner",
  },

  {
    id: "04",
    category: "Supper",
  },

  {
    id: "05",
    category: "BreaLu",
  },

  {
    id: "06",
    category: "Lupper",
  },

  {
    id: "07",
    category: "Brealuper",
  },
];

// Different Foods
export const foodList = [
  {
    id: "01",
    name: "Chips",
    image: require("../assets/images/image1.jpeg"),
    rating: "5.0",
    ingredients: ["Tomato Source", "Tomatoes", "Onions"],
    time: "1 hour",
    dificulty: "Medium",
    calories: "420 cal",
    color: "green",
    description:
      "gjdfgbhdwfdsv  dvvdvhdhd dgdkddwjgbdj dggdhdjghjdd dggddjjhdgd",
    steps: [
      "1. fghhdd dhhjdjd ddhhddjjdhddn",
      "2.hhhh dhgdewkgvkm ekuemdmjm  ddgh",
      "3. gtgdgbgehd jkhegkeyge hhgdgdh",
      "4. hhfgdd djyhkyuf djdjghjf",
    ],
  },

  {
    id: "02",
    name: "Chicken",
    image: require("../assets/images/image2.jpeg"),
    rating: "5.0",
    ingredients: ["Cooking Oil", "Tomatoes", "Onions"],
    time: "1.5 hours",
    dificulty: "Medium",
    calories: "450 cal",
    color: "#f0f",
    description:
      "gjdfgbhdwfdsv  dvvdvhdhd dgdkddwjgbdj dggdhdjghjdd dggddjjhdgd",
    steps: [
      "1. fghhdd dhhjdjd ddhhddjjdhddn",
      "2.hhhh dhgdewkgvkm ekuemdmjm  ddgh",
      "3. gtgdgbgehd jkhegkeyge hhgdgdh",
      "4. hhfgdd djyhkyuf djdjghjf",
    ],
  },

  {
    id: "03",
    name: "Buffe",
    image: require("../assets/images/image3.jpeg"),
    rating: "5.0",
    ingredients: ["Cooking Oil", "Tomatoes", "Onions"],
    time: "3.5 hours",
    dificulty: "Hard",
    calories: "550 cal",
    color: "red",
    description:
      "gjdfgbhdwfdsv  dvvdvhdhd dgdkddwjgbdj dggdhdjghjdd dggddjjhdgd",
    steps: [
      "1. fghhdd dhhjdjd ddhhddjjdhddn",
      "2.hhhh dhgdewkgvkm ekuemdmjm  ddgh",
      "3. gtgdgbgehd jkhegkeyge hhgdgdh",
      "4. hhfgdd djyhkyuf djdjghjf",
    ],
  },

  {
    id: "04",
    name: "Pizza",
    image: require("../assets/images/image4.jpeg"),
    rating: "5.0",
    ingredients: ["Cooking Oil", "Tomatoes", "Onions"],
    time: "1 hour",
    dificulty: "Medium",
    calories: "380 cal",
    color: "#262626",
    description:
      "gjdfgbhdwfdsv  dvvdvhdhd dgdkddwjgbdj dggdhdjghjdd dggddjjhdgd",
    steps: [
      "1. fghhdd dhhjdjd ddhhddjjdhddn",
      "2.hhhh dhgdewkgvkm ekuemdmjm  ddgh",
      "3. gtgdgbgehd jkhegkeyge hhgdgdh",
      "4. hhfgdd djyhkyuf djdjghjf",
    ],
  },

  {
    id: "05",
    name: "Barger",
    image: require("../assets/images/image5.jpeg"),
    rating: "4.5",
    ingredients: ["Cooking Oil", "Tomatoes", "Onions"],
    time: "1 hour",
    dificulty: "Hard",
    calories: "470 cal",
    color: "#808080",
    description:
      "gjdfgbhdwfdsv  dvvdvhdhd dgdkddwjgbdj dggdhdjghjdd dggddjjhdgd",
    steps: [
      "1. fghhdd dhhjdjd ddhhddjjdhddn",
      "2.hhhh dhgdewkgvkm ekuemdmjm  ddgh",
      "3. gtgdgbgehd jkhegkeyge hhgdgdh",
      "4. hhfgdd djyhkyuf djdjghjf",
    ],
  },

  {
    id: "06",
    name: "Macron",
    image: require("../assets/images/image6.jpeg"),
    rating: "3.4",
    ingredients: ["Cooking Oil", "Tomatoes", "Onions"],
    time: "2 hours",
    dificulty: "Medium",
    calories: "250 cal",
    color: "#268000",
    description:
      "gjdfgbhdwfdsv  dvvdvhdhd dgdkddwjgbdj dggdhdjghjdd dggddjjhdgd",
    steps: [
      "1. fghhdd dhhjdjd ddhhddjjdhddn",
      "2.hhhh dhgdewkgvkm ekuemdmjm  ddgh",
      "3. gtgdgbgehd jkhegkeyge hhgdgdh",
      "4. hhfgdd djyhkyuf djdjghjf",
    ],
  },

  {
    id: "07",
    name: "Lusaniya",
    image: require("../assets/images/image7.jpeg"),
    rating: "5.0",
    ingredients: ["Cooking Oil", "Tomatoes", "Onions"],
    time: "3 hours",
    dificulty: "Hard",
    calories: "500 cal",
    color: "#f00",
    description:
      "gjdfgbhdwfdsv  dvvdvhdhd dgdkddwjgbdj dggdhdjghjdd dggddjjhdgd",
    steps: [
      "1. fghhdd dhhjdjd ddhhddjjdhddn",
      "2.hhhh dhgdewkgvkm ekuemdmjm  ddgh",
      "3. gtgdgbgehd jkhegkeyge hhgdgdh",
      "4. hhfgdd djyhkyuf djdjghjf",
    ],
  },

  {
    id: "08",
    name: "Katogo",
    image: require("../assets/images/image8.jpeg"),
    rating: "3.0",
    ingredients: ["Cooking Oil", "Tomatoes", "Onions"],
    time: "2 hours",
    dificulty: "Medium",
    calories: "300 cal",
    color: "purple",
    description:
      "gjdfgbhdwfdsv  dvvdvhdhd dgdkddwjgbdj dggdhdjghjdd dggddjjhdgd",
    steps: [
      "1. fghhdd dhhjdjd ddhhddjjdhddn",
      "2.hhhh dhgdewkgvkm ekuemdmjm  ddgh",
      "3. gtgdgbgehd jkhegkeyge hhgdgdh",
      "4. hhfgdd djyhkyuf djdjghjf",
    ],
  },

  {
    id: "09",
    name: "Fish",
    image: require("../assets/images/image9.jpeg"),
    rating: "4.8",
    ingredients: ["Cooking Oil", "Tomatoes", "Onions"],
    time: "1.5 hours",
    dificulty: "Medium",
    calories: "450 cal",
    color: "indigo",
    description:
      "gjdfgbhdwfdsv  dvvdvhdhd dgdkddwjgbdj dggdhdjghjdd dggddjjhdgd",
    steps: [
      "1. fghhdd dhhjdjd ddhhddjjdhddn",
      "2.hhhh dhgdewkgvkm ekuemdmjm  ddgh",
      "3. gtgdgbgehd jkhegkeyge hhgdgdh",
      "4. hhfgdd djyhkyuf djdjghjf",
    ],
  },

  {
    id: "10",
    name: "Rolex",
    image: require("../assets/images/image10.jpeg"),
    rating: "4.3",
    ingredients: ["Cooking Oil", "Tomatoes", "Onions"],
    time: "30 mins",
    dificulty: "Medium",
    calories: "320 cal",
    color: "orange",
    description:
      "gjdfgbhdwfdsv  dvvdvhdhd dgdkddwjgbdj dggdhdjghjdd dggddjjhdgd",
    steps: [
      "1. fghhdd dhhjdjd ddhhddjjdhddn",
      "2.hhhh dhgdewkgvkm ekuemdmjm  ddgh",
      "3. gtgdgbgehd jkhegkeyge hhgdgdh",
      "4. hhfgdd djyhkyuf djdjghjf",
    ],
  },

  {
    id: "11",
    name: "Local Foods",
    image: require("../assets/images/image11.jpeg"),
    rating: "3.5",
    ingredients: ["Cooking Oil", "Tomatoes", "Onions"],
    time: "2.5 hours",
    dificulty: "Medium",
    calories: "320 cal",
    color: "lightblue",
    description:
      "gjdfgbhdwfdsv  dvvdvhdhd dgdkddwjgbdj dggdhdjghjdd dggddjjhdgd",
    steps: [
      "1. fghhdd dhhjdjd ddhhddjjdhddn",
      "2.hhhh dhgdewkgvkm ekuemdmjm  ddgh",
      "3. gtgdgbgehd jkhegkeyge hhgdgdh",
      "4. hhfgdd djyhkyuf djdjghjf",
    ],
  },

  {
    id: "12",
    name: "Luwombo",
    image: require("../assets/images/image12.jpeg"),
    rating: "5.0",
    ingredients: ["Cooking Oil", "Tomatoes", "Onions"],
    time: "3 hours",
    dificulty: "Medium",
    calories: "320 cal",
    color: "lightgray",
    description:
      "gjdfgbhdwfdsv  dvvdvhdhd dgdkddwjgbdj dggdhdjghjdd dggddjjhdgd",
    steps: [
      "1. fghhdd dhhjdjd ddhhddjjdhddn",
      "2.hhhh dhgdewkgvkm ekuemdmjm  ddgh",
      "3. gtgdgbgehd jkhegkeyge hhgdgdh",
      "4. hhfgdd djyhkyuf djdjghjf",
    ],
  },
];
