import { StyleSheet, View, Text } from "react-native";
import { React, useState, useEffect } from "react";
import Header from "../components/Header";
import { SafeAreaView } from "react-native-safe-area-context";
import SearchFilter from "../components/SearchFilter";
import CategoriesFilter from "../components/CategoriesFilter";
import FoodsCard from "../components/FoodsCard";
import { categories, foodList } from "../Constants";

const FoodsListScreen = ({ route }) => {
  const [query, setQuery] = useState("");
  const [filteredFoodList, setFilteredFoodList] = useState(foodList);
  const [filteredCategory, setFilteredCategory] = useState(categories);

  useEffect(() => {
    // This is a search logic for the foods
    setFilteredFoodList(
      foodList.filter((food) =>
        food.name.toLowerCase().includes(query.toLowerCase())
      )
    );

    // This is a search logic for thw categories
    setFilteredCategory(
      categories.filter((category) =>
        category.category.toLowerCase().includes(query.toLowerCase())
      )
    );
  }, [query, foodList, categories]);

  // Receiving the username that has been Entered when logging in
  const { username } = route.params;

  // const handleSearch = (text) => {
  //   setQuery(text);
  //   const newFoodList = foodList.filter((item) =>
  //     item.name.toLowerCase().includes(text.toLowerCase())
  //   );
  //   setFilteredFoodList(newFoodList);
  // };
  return (
    <SafeAreaView style={{ flex: 1, marginHorizontal: 16 }}>
      <Header headerText={username} headerIcon={"bell-o"} />
      {/* The SearchFilter Component */}
      <View style={{ marginTop: 20 }}>
        <SearchFilter
          icon={"search"}
          placeholder={"Enter your favourite meal"}
          query={query}
          setQuery={setQuery}
        />
      </View>

      <View style={{ marginTop: 22 }}>
        <Text
          style={{
            fontSize: 25,
            fontWeight: "bold",
            fontFamily: "serif",
            marginBottom: 10,
          }}
        >
          Categories
        </Text>
        {/* CategoriesFilter rendered */}
        <CategoriesFilter categories={filteredCategory} />
      </View>

      {/* Foods List */}
      <View style={{ marginTop: 22, flex: 1 }}>
        <Text
          style={{
            fontSize: 28,
            fontWeight: "bold",
            fontFamily: "serif",
            marginBottom: 10,
          }}
        >
          Popular Foods
        </Text>
        {/* CategoriesFilter rendered */}
        <FoodsCard foodList={filteredFoodList} />
      </View>
    </SafeAreaView>
  );
};

export default FoodsListScreen;

const styles = StyleSheet.create({});
